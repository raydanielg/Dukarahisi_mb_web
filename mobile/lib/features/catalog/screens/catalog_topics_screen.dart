import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/services/catalog_service.dart';
import '../../../core/network/api_client.dart';

class CatalogTopicsScreen extends StatefulWidget {
  const CatalogTopicsScreen({super.key});

  @override
  State<CatalogTopicsScreen> createState() => _CatalogTopicsScreenState();
}

class _CatalogTopicsScreenState extends State<CatalogTopicsScreen> {
  late final CatalogService _catalogService;
  List<Map<String, dynamic>>? _topics;
  bool _loading = true;
  int? _subjectId;
  String? _materialType;

  @override
  void initState() {
    super.initState();
    _catalogService = CatalogService(ApiClient());
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final extra = GoRouterState.of(context).extra as Map<String, dynamic>?;
      if (extra != null) {
        _subjectId = extra['subjectId'] as int?;
        _materialType = extra['materialType'] as String?;
        if (_subjectId != null) {
          _loadTopics();
        }
      }
    });
  }

  Future<void> _loadTopics() async {
    try {
      final response = await _catalogService.getTopics(_subjectId!, materialType: _materialType);
      if (mounted) {
        setState(() {
          _topics = (response['data'] as List).map((e) => e as Map<String, dynamic>).toList();
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _topics = [];
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
                    'Topics',
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
                  : _topics == null || _topics!.isEmpty
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
                                'No topics yet',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                            ],
                          ),
                        )
                      : ListView.separated(
                          padding: const EdgeInsets.all(16),
                          itemCount: _topics!.length,
                          separatorBuilder: (context, index) => const SizedBox(height: 12),
                          itemBuilder: (context, index) {
                            final topic = _topics![index];
                            return _buildTopicListItem(topic, index + 1);
                          },
                        ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTopicListItem(Map<String, dynamic> topic, int orderNumber) {
    return GestureDetector(
      onTap: () => context.push('/catalog-materials', extra: {
        'topicId': topic['id'],
        'materialType': _materialType,
      }),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.primary.withOpacity(0.12), width: 1.5),
          boxShadow: [
            BoxShadow(
              color: AppColors.primary.withOpacity(0.06),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Center(
                  child: Text(
                    '$orderNumber',
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: AppColors.primary,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Text(
                  topic['name']?.toString() ?? 'Topic',
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
