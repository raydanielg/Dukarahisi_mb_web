import 'dart:async';
import 'dart:io';
import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import 'package:path_provider/path_provider.dart';
import 'package:flutter_pdfview/flutter_pdfview.dart';
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
        final loadedMaterials = (response['data'] as List).map((e) => e as Map<String, dynamic>).toList();
        setState(() {
          _materials = loadedMaterials;
          _loading = false;
        });
        if (loadedMaterials.length == 1) {
          _openPdfViewer(loadedMaterials.first);
        }
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

  void _openPdfViewer(Map<String, dynamic> material) {
    final bool hasAccess = material['has_purchased'] == true || material['is_free'] == true;
    final String? fileUrl = material['file_url']?.toString();

    if (!hasAccess || fileUrl == null || fileUrl.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Purchase this material to view the PDF'),
          backgroundColor: AppColors.textPrimary,
        ),
      );
      return;
    }

    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (context) => _PdfViewerScreen(url: fileUrl, title: material['title']?.toString() ?? 'Document'),
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
    final bool hasAccess = material['has_purchased'] == true || material['is_free'] == true;
    final String? fileUrl = material['file_url']?.toString();

    return Container(
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
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.08),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Icon(
                Icons.description_outlined,
                color: AppColors.primary,
                size: 24,
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Text(
                material['title']?.toString() ?? 'Material',
                style: const TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const SizedBox(width: 10),
            GestureDetector(
              onTap: () => _openPdfViewer(material),
              child: Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.visibility,
                  color: AppColors.primary,
                  size: 20,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

}

class _PdfViewerScreen extends StatefulWidget {
  final String url;
  final String title;

  const _PdfViewerScreen({required this.url, required this.title});

  @override
  State<_PdfViewerScreen> createState() => _PdfViewerScreenState();
}

class _PdfViewerScreenState extends State<_PdfViewerScreen> {
  String? _localFilePath;
  bool _loading = true;
  String? _error;
  int _pages = 0;
  int _currentPage = 0;
  bool _isReady = false;
  final Completer<PDFViewController> _controller = Completer<PDFViewController>();

  @override
  void initState() {
    super.initState();
    _downloadPdf();
  }

  Future<void> _downloadPdf() async {
    try {
      final apiClient = ApiClient();
      final response = await apiClient.dio.get<List<int>>(
        widget.url,
        options: Options(responseType: ResponseType.bytes),
      );
      final bytes = Uint8List.fromList(response.data ?? []);

      final dir = await getTemporaryDirectory();
      final fileName = widget.url.split('/').last.isNotEmpty
          ? widget.url.split('/').last
          : 'document.pdf';
      final file = File('${dir.path}/$fileName');
      await file.writeAsBytes(bytes);

      if (mounted) {
        setState(() {
          _localFilePath = file.path;
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = 'Failed to load PDF: ${e.toString()}';
          _loading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: AppColors.textPrimary),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: Text(
          widget.title,
          style: const TextStyle(
            color: AppColors.textPrimary,
            fontSize: 16,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
          : _error != null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.error_outline, color: Colors.red[400], size: 48),
                      const SizedBox(height: 16),
                      Text(
                        _error!,
                        style: TextStyle(color: AppColors.textSecondary),
                      ),
                    ],
                  ),
                )
              : PDFView(
                  filePath: _localFilePath,
                  enableSwipe: true,
                  swipeHorizontal: true,
                  autoSpacing: false,
                  pageFling: false,
                  backgroundColor: Colors.grey[200],
                  onRender: (pages) {
                    setState(() {
                      _pages = pages ?? 0;
                      _isReady = true;
                    });
                  },
                  onError: (error) {
                    setState(() => _error = 'Failed to load PDF');
                  },
                  onPageError: (page, error) {
                    setState(() => _error = 'Failed to load page $page');
                  },
                  onViewCreated: (PDFViewController pdfViewController) {
                    _controller.complete(pdfViewController);
                  },
                  onPageChanged: (int? page, int? total) {
                    setState(() {
                      _currentPage = page ?? 0;
                      _pages = total ?? 0;
                    });
                  },
                ),
    );
  }
}
