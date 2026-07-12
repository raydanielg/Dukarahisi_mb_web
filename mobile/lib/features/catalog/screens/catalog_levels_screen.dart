import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/services/catalog_service.dart';
import '../../../core/network/api_client.dart';

class CatalogLevelsScreen extends StatefulWidget {
  const CatalogLevelsScreen({super.key});

  @override
  State<CatalogLevelsScreen> createState() => _CatalogLevelsScreenState();
}

class _CatalogLevelsScreenState extends State<CatalogLevelsScreen> {
  late final CatalogService _catalogService;
  List<Map<String, dynamic>>? _levels;
  bool _loading = true;
  String? _materialType;

  @override
  void initState() {
    super.initState();
    _catalogService = CatalogService(ApiClient());
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _materialType = GoRouterState.of(context).extra as String?;
      print('Material type received: $_materialType');
      _loadLevels();
    });
  }

  Future<void> _loadLevels() async {
    try {
      final response = await _catalogService.getLevels(materialType: _materialType);
      print('Levels response: $response');
      print('Levels data: ${response['data']}');
      print('Levels data type: ${response['data'].runtimeType}');
      if (mounted) {
        setState(() {
          _levels = (response['data'] as List).map((e) => e as Map<String, dynamic>).toList();
          _loading = false;
        });
        print('Levels set: $_levels');
        print('Levels is empty: ${_levels?.isEmpty}');
      }
    } catch (e) {
      print('Error loading levels: $e');
      if (mounted) {
        setState(() {
          _levels = [];
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
                    'Levels',
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
                  : _levels == null || _levels!.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Image.asset(
                                'assets/icons/level.png',
                                width: 80,
                                height: 80,
                                fit: BoxFit.contain,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'No levels yet',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                            ],
                          ),
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.all(16),
                          itemCount: _levels!.length,
                          itemBuilder: (context, index) {
                            final level = _levels![index];
                            return _buildLevelCard(level);
                          },
                        ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLevelCard(Map<String, dynamic> level) {
    final iconPath = level['icon']?.toString();

    return GestureDetector(
      onTap: () => context.push('/catalog-classes', extra: {
        'levelId': level['id'],
        'materialType': _materialType,
      }),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white,
              Colors.grey[50]!,
            ],
          ),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: AppColors.primary.withOpacity(0.15), width: 1),
          boxShadow: [
            BoxShadow(
              color: AppColors.primary.withOpacity(0.08),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [AppColors.primary, AppColors.primaryDark],
                  ),
                  borderRadius: BorderRadius.circular(14),
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.primary.withOpacity(0.3),
                      blurRadius: 8,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: iconPath != null && iconPath.isNotEmpty
                    ? Padding(
                        padding: const EdgeInsets.all(10),
                        child: Image.asset(
                          iconPath,
                          fit: BoxFit.contain,
                          color: Colors.white,
                        ),
                      )
                    : const Icon(
                        Icons.school,
                        color: Colors.white,
                        size: 28,
                      ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      level['name']?.toString() ?? 'Level',
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      level['description']?.toString() ?? 'Select this level',
                      style: TextStyle(
                        fontSize: 12,
                        color: AppColors.textSecondary,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.chevron_right_rounded,
                  color: AppColors.primary,
                  size: 22,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
