import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';

class CatalogSubLevelsScreen extends StatefulWidget {
  const CatalogSubLevelsScreen({super.key});

  @override
  State<CatalogSubLevelsScreen> createState() => _CatalogSubLevelsScreenState();
}

class _CatalogSubLevelsScreenState extends State<CatalogSubLevelsScreen> {
  int? _levelId;
  String? _levelName;
  String? _levelIcon;
  String? _materialType;
  List<Map<String, dynamic>>? _subLevels;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final extra = GoRouterState.of(context).extra as Map<String, dynamic>?;
      if (extra != null) {
        _levelId = extra['levelId'] as int?;
        _levelName = extra['levelName'] as String?;
        _levelIcon = extra['levelIcon'] as String?;
        _materialType = extra['materialType'] as String?;
        _subLevels = (extra['subLevels'] as List?)?.map((e) => e as Map<String, dynamic>).toList();
        if (mounted) setState(() {});
      }
    });
  }

  Widget _buildLevelIcon() {
    final iconPath = _levelIcon;
    if (iconPath != null && iconPath.isNotEmpty) {
      if (iconPath.startsWith('http')) {
        return Image.network(
          iconPath,
          width: 50,
          height: 50,
          fit: BoxFit.contain,
          errorBuilder: (context, error, stackTrace) => const Icon(
            Icons.school,
            color: AppColors.primary,
            size: 32,
          ),
        );
      }
      return Image.asset(
        iconPath,
        width: 50,
        height: 50,
        fit: BoxFit.contain,
        errorBuilder: (context, error, stackTrace) => const Icon(
          Icons.school,
          color: AppColors.primary,
          size: 32,
        ),
      );
    }
    return const Icon(Icons.school, color: AppColors.primary, size: 32);
  }

  Widget _buildSubLevelCard(Map<String, dynamic> subLevel) {
    return GestureDetector(
      onTap: () => context.push('/catalog-classes', extra: {
        'levelId': _levelId,
        'subLevelId': subLevel['id'],
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
              _buildLevelIcon(),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      subLevel['name']?.toString() ?? 'Sub-Level',
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      subLevel['description']?.toString() ?? 'Select this sub-level',
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

  Widget _buildNoSubLevelCard() {
    return GestureDetector(
      onTap: () => context.push('/catalog-classes', extra: {
        'levelId': _levelId,
        'subLevelId': 'none',
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
              _buildLevelIcon(),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'General Classes',
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Classes without a specific sub-level',
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
                  Expanded(
                    child: Text(
                      _levelName != null ? '$_levelName - Sub-Levels' : 'Sub-Levels',
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              child: _subLevels == null
                  ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
                  : _subLevels!.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              _buildLevelIcon(),
                              const SizedBox(height: 16),
                              Text(
                                'No sub-levels yet',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                              const SizedBox(height: 24),
                              _buildNoSubLevelCard(),
                            ],
                          ),
                        )
                      : ListView(
                          padding: const EdgeInsets.all(16),
                          children: [
                            ..._subLevels!.map((s) => _buildSubLevelCard(s)),
                            _buildNoSubLevelCard(),
                          ],
                        ),
            ),
          ],
        ),
      ),
    );
  }
}
