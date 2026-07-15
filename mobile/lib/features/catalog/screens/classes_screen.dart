import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/services/catalog_service.dart';
import '../../../core/network/api_client.dart';

class ClassesScreen extends StatefulWidget {
  const ClassesScreen({super.key});

  @override
  State<ClassesScreen> createState() => _ClassesScreenState();
}

class _ClassesScreenState extends State<ClassesScreen> {
  late final CatalogService _catalogService;
  List<Map<String, dynamic>>? _classes;
  bool _loading = true;
  int? _levelId;

  @override
  void initState() {
    super.initState();
    _catalogService = CatalogService(ApiClient());
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _levelId = GoRouterState.of(context).extra as int?;
      if (_levelId != null) {
        _loadClasses();
      }
    });
  }

  Future<void> _loadClasses() async {
    try {
      final response = await _catalogService.getClasses(_levelId!);
      if (mounted) {
        setState(() {
          _classes = response['data'];
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _classes = [];
          _loading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  IconButton(
                    icon: const Icon(Icons.arrow_back, color: AppColors.textPrimary),
                    onPressed: () => context.pop(),
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    'Darasa',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              child: _loading
                  ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
                  : _classes == null || _classes!.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.class_outlined,
                                size: 64,
                                color: AppColors.textMuted,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Hakuna darasa bado',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                            ],
                          ),
                        )
                      : _buildGroupedClasses(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildGroupedClasses() {
    final hasMediums = _classes!.any((c) => c['medium'] != null && c['medium'].toString().isNotEmpty);

    if (!hasMediums) {
      return GridView.builder(
        padding: const EdgeInsets.all(16),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          crossAxisSpacing: 16,
          mainAxisSpacing: 16,
          childAspectRatio: 1.2,
        ),
        itemCount: _classes!.length,
        itemBuilder: (context, index) {
          final classItem = _classes![index];
          return _buildClassCard(classItem);
        },
      );
    }

    final groups = <String, List<Map<String, dynamic>>>{};
    for (final c in _classes!) {
      final medium = (c['medium']?.toString() ?? 'general').toLowerCase();
      groups.putIfAbsent(medium, () => []).add(c);
    }

    final orderedKeys = groups.keys.toList()
      ..sort((a, b) {
        if (a == 'english') return -1;
        if (b == 'english') return 1;
        if (a == 'kiswahili') return -1;
        if (b == 'kiswahili') return 1;
        return 0;
      });

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: orderedKeys.length,
      itemBuilder: (context, index) {
        final medium = orderedKeys[index];
        final items = groups[medium]!;
        final label = medium == 'english'
            ? 'English Medium'
            : medium == 'kiswahili'
                ? 'Kiswahili Medium'
                : 'General';
        final badgeColor = medium == 'english'
            ? const Color(0xFF3B82F6)
            : medium == 'kiswahili'
                ? const Color(0xFF10B981)
                : AppColors.textMuted;

        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (index > 0) const SizedBox(height: 24),
            Row(
              children: [
                Container(
                  width: 8,
                  height: 8,
                  decoration: BoxDecoration(shape: BoxShape.circle, color: badgeColor),
                ),
                const SizedBox(width: 8),
                Text(
                  label,
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: badgeColor,
                  ),
                ),
                const SizedBox(width: 8),
                Text(
                  '${items.length} classes',
                  style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
                ),
              ],
            ),
            const SizedBox(height: 12),
            GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
                childAspectRatio: 1.2,
              ),
              itemCount: items.length,
              itemBuilder: (context, index) {
                return _buildClassCard(items[index]);
              },
            ),
          ],
        );
      },
    );
  }

  Widget _buildClassCard(Map<String, dynamic> classItem) {
    final medium = classItem['medium']?.toString();
    final mediumLabel = medium == 'english'
        ? 'English'
        : medium == 'kiswahili'
            ? 'Kiswahili'
            : null;
    final mediumColor = medium == 'english'
        ? const Color(0xFF3B82F6)
        : medium == 'kiswahili'
            ? const Color(0xFF10B981)
            : AppColors.textMuted;

    return GestureDetector(
      onTap: () => context.push('/subjects', extra: classItem['id']),
      child: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white,
              Colors.white.withOpacity(0.95),
            ],
          ),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.white.withOpacity(0.3), width: 1.5),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                width: 60,
                height: 60,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [AppColors.accent, Color(0xFFE59E0B)],
                  ),
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.accent.withOpacity(0.3),
                      blurRadius: 12,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: const Icon(
                  Icons.class_,
                  color: Colors.white,
                  size: 32,
                ),
              ),
              const SizedBox(height: 16),
              Text(
                classItem['name']?.toString() ?? 'Class',
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
                textAlign: TextAlign.center,
              ),
              if (mediumLabel != null) ...[
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(
                    color: mediumColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    mediumLabel,
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: mediumColor,
                    ),
                  ),
                ),
              ],
              const SizedBox(height: 4),
              Text(
                classItem['description']?.toString() ?? '',
                style: TextStyle(
                  fontSize: 12,
                  color: AppColors.textSecondary,
                ),
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
