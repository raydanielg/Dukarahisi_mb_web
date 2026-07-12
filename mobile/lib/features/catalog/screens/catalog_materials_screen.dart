import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/services/catalog_service.dart';
import '../../../core/network/api_client.dart';

class CatalogMaterialsScreen extends StatefulWidget {
  const CatalogMaterialsScreen({super.key});

  @override
  State<CatalogMaterialsScreen> createState() => _CatalogMaterialsScreenState();
}

class _CatalogMaterialsScreenState extends State<CatalogMaterialsScreen> {
  late final CatalogService _catalogService;
  List<Map<String, dynamic>>? _materials;
  bool _loading = true;
  int? _topicId;
  String? _materialType;

  @override
  void initState() {
    super.initState();
    _catalogService = CatalogService(ApiClient());
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final extra = GoRouterState.of(context).extra as Map<String, dynamic>?;
      if (extra != null) {
        _topicId = extra['topicId'] as int?;
        _materialType = extra['materialType'] as String?;
        if (_topicId != null) {
          _loadMaterials();
        }
      }
    });
  }

  Future<void> _loadMaterials() async {
    try {
      final response = await _catalogService.getMaterials(_topicId!, materialType: _materialType);
      if (mounted) {
        setState(() {
          _materials = (response['data'] as List).map((e) => e as Map<String, dynamic>).toList();
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _materials = [];
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
                  Text(
                    _getMaterialTypeTitle(),
                    style: const TextStyle(
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
                  : _materials == null || _materials!.isEmpty
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
                                'No materials yet',
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
                          itemCount: _materials!.length,
                          itemBuilder: (context, index) {
                            final material = _materials![index];
                            return _buildMaterialCard(material);
                          },
                        ),
            ),
          ],
        ),
      ),
    );
  }

  String _getMaterialTypeTitle() {
    switch (_materialType) {
      case 'notes':
        return 'Notes';
      case 'books':
        return 'Books';
      case 'lesson-plans':
        return 'Lesson Plans';
      case 'syllabus':
        return 'Syllabus';
      case 'scheme-of-work':
        return 'Scheme of Work';
      case 'logbooks':
        return 'Logbooks';
      default:
        return 'Materials';
    }
  }

  Widget _buildMaterialCard(Map<String, dynamic> material) {
    final rawPrice = material['price'];
    final double price = rawPrice is num ? rawPrice.toDouble() : (double.tryParse(rawPrice?.toString() ?? '0') ?? 0);
    final bool isFree = material['is_free'] == true || price <= 0;
    final hasPurchased = material['has_purchased'] ?? false;

    return GestureDetector(
      onTap: () {
        // Navigate to material detail or purchase
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.primary.withOpacity(0.12), width: 1.5),
          boxShadow: [
            BoxShadow(
              color: AppColors.primary.withOpacity(0.06),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Container(
                width: 56,
                height: 72,
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(
                  Icons.description_outlined,
                  color: AppColors.primary,
                  size: 28,
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      material['title']?.toString() ?? 'Material',
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 6),
                    isFree
                        ? Row(
                            children: [
                              Icon(Icons.check_circle, color: AppColors.success, size: 14),
                              const SizedBox(width: 4),
                              Text(
                                'This source is free',
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.success,
                                ),
                              ),
                            ],
                          )
                        : Row(
                            children: [
                              Icon(Icons.monetization_on, color: AppColors.accent, size: 14),
                              const SizedBox(width: 4),
                              Text(
                                'TZS ${price.toStringAsFixed(2)}',
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.accent,
                                ),
                              ),
                            ],
                          ),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: AppColors.primary.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        (material['file_type']?.toString() ?? 'PDF').toUpperCase(),
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary,
                        ),
                      ),
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
                  size: 20,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
