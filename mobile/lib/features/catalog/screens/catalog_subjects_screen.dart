import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/services/catalog_service.dart';
import '../../../core/network/api_client.dart';

class CatalogSubjectsScreen extends StatefulWidget {
  const CatalogSubjectsScreen({super.key});

  @override
  State<CatalogSubjectsScreen> createState() => _CatalogSubjectsScreenState();
}

class _CatalogSubjectsScreenState extends State<CatalogSubjectsScreen> {
  late final CatalogService _catalogService;
  List<Map<String, dynamic>>? _subjects;
  List<Map<String, dynamic>> _filteredSubjects = [];
  bool _loading = true;
  int? _classId;
  String? _materialType;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _catalogService = CatalogService(ApiClient());
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final extra = GoRouterState.of(context).extra as Map<String, dynamic>?;
      if (extra != null) {
        _classId = extra['classId'] as int?;
        _materialType = extra['materialType'] as String?;
        if (_classId != null) {
          _loadSubjects();
        }
      }
    });
  }

  Future<void> _loadSubjects() async {
    try {
      final response = await _catalogService.getSubjects(_classId!, materialType: _materialType);
      if (mounted) {
        setState(() {
          _subjects = (response['data'] as List).map((e) => e as Map<String, dynamic>).toList();
          _filterSubjects();
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _subjects = [];
          _filteredSubjects = [];
          _loading = false;
        });
      }
    }
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _filterSubjects() {
    final query = _searchController.text.toLowerCase().trim();
    if (query.isEmpty) {
      _filteredSubjects = List<Map<String, dynamic>>.from(_subjects ?? []);
    } else {
      _filteredSubjects = (_subjects ?? []).where((subject) {
        final name = subject['name']?.toString().toLowerCase() ?? '';
        return name.contains(query);
      }).toList();
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
                    'Subjects',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: TextField(
                controller: _searchController,
                onChanged: (value) => setState(() => _filterSubjects()),
                decoration: InputDecoration(
                  hintText: 'Search subjects',
                  hintStyle: TextStyle(
                    fontSize: 14,
                    color: AppColors.textSecondary.withOpacity(0.5),
                  ),
                  prefixIcon: Icon(Icons.search, color: AppColors.primary.withOpacity(0.5), size: 20),
                  suffixIcon: _searchController.text.isNotEmpty
                      ? GestureDetector(
                          onTap: () {
                            _searchController.clear();
                            setState(() => _filterSubjects());
                          },
                          child: Icon(Icons.clear, color: AppColors.primary.withOpacity(0.5), size: 18),
                        )
                      : null,
                  filled: true,
                  fillColor: Colors.grey[50],
                  contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: AppColors.primary.withOpacity(0.12)),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: AppColors.primary.withOpacity(0.12)),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: AppColors.primary.withOpacity(0.4)),
                  ),
                ),
              ),
            ),
            Expanded(
              child: _loading
                  ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
                  : _filteredSubjects.isEmpty
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
                                _searchController.text.isEmpty
                                    ? 'No subjects yet'
                                    : 'No subjects found',
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
                          itemCount: _filteredSubjects.length,
                          separatorBuilder: (context, index) => const SizedBox(height: 12),
                          itemBuilder: (context, index) {
                            final subject = _filteredSubjects[index];
                            return _buildSubjectListItem(subject, index + 1);
                          },
                        ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSubjectListItem(Map<String, dynamic> subject, int orderNumber) {
    return GestureDetector(
      onTap: () => context.push('/catalog-topics', extra: {
        'subjectId': subject['id'],
        'materialType': _materialType,
      }),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.primary.withOpacity(0.12), width: 1.5),
          boxShadow: [
            BoxShadow(
              color: AppColors.primary.withOpacity(0.06),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Image.asset(
                'assets/foldericon.png',
                width: 38,
                height: 38,
                fit: BoxFit.contain,
                errorBuilder: (context, error, stackTrace) => Container(
                  width: 38,
                  height: 38,
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: Center(
                    child: Text(
                      '$orderNumber',
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: AppColors.primary,
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  subject['name']?.toString() ?? 'Subject',
                  style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary,
                  ),
                ),
              ),
              const SizedBox(width: 6),
              const Icon(
                Icons.arrow_forward_ios,
                color: AppColors.primary,
                size: 16,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
