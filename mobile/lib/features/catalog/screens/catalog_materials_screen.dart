import 'dart:async';
import 'dart:io';
import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import 'package:path_provider/path_provider.dart';
import 'package:flutter_pdfview/flutter_pdfview.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/config/app_config.dart';
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
    final String? fileUrl = material['file_url']?.toString();

    if (fileUrl == null || fileUrl.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('PDF not available'),
          backgroundColor: AppColors.textPrimary,
        ),
      );
      return;
    }

    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (context) => _PdfViewerScreen(
          url: fileUrl,
          title: material['title']?.toString() ?? 'Document',
          material: material,
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
  final Map<String, dynamic> material;

  const _PdfViewerScreen({required this.url, required this.title, required this.material});

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
  double _zoom = 1.0;
  PDFViewController? _pdfController;
  final Completer<PDFViewController> _controller = Completer<PDFViewController>();

  bool get _hasAccess =>
      widget.material['has_purchased'] == true || widget.material['is_free'] == true;

  bool get _isFree => widget.material['is_free'] == true;

  static const int _previewPageLimit = 2;

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

  Future<void> _downloadFileToDevice() async {
    try {
      final apiClient = ApiClient();
      final response = await apiClient.dio.get<List<int>>(
        widget.url,
        options: Options(responseType: ResponseType.bytes),
      );
      final bytes = Uint8List.fromList(response.data ?? []);

      final dir = await getDownloadsDirectory() ?? await getApplicationDocumentsDirectory();
      final fileName = '${widget.title}.pdf'.replaceAll(RegExp(r'[^\w\s-]'), '').replaceAll(' ', '_');
      final file = File('${dir.path}/$fileName');
      await file.writeAsBytes(bytes);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('PDF saved to ${file.path}'),
            backgroundColor: AppColors.primary,
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Failed to download PDF: ${e.toString()}'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  void _goToCheckout() {
    final price = (widget.material['price'] as num?)?.toDouble() ?? 0.0;
    final materialType = widget.material['material_type']?.toString() ?? 'notes';
    final id = widget.material['id'] as int? ?? 0;
    context.push('/checkout', extra: {
      'id': id,
      'type': materialType,
      'title': widget.title,
      'price': price,
    });
  }

  Future<void> _jumpToPage() async {
    final controller = await _controller.future;
    final textController = TextEditingController();
    final page = await showDialog<int>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Go to page'),
        content: TextField(
          controller: textController,
          keyboardType: TextInputType.number,
          decoration: InputDecoration(
            hintText: '1 - $_pages',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Cancel')),
          TextButton(
            onPressed: () {
              final value = int.tryParse(textController.text);
              Navigator.pop(context, value);
            },
            child: const Text('Go'),
          ),
        ],
      ),
    );
    if (page != null && page > 0 && page <= _pages) {
      controller.setPage(page - 1);
    }
  }

  Future<void> _setZoom(double value) async {
    setState(() => _zoom = value);
    final controller = await _controller.future;
    controller.setPage(_currentPage);
  }

  Future<void> _refreshPageInfo() async {
    final controller = await _controller.future;
    final current = await controller.getCurrentPage();
    final total = await controller.getPageCount();
    if (mounted && current != null && total != null) {
      setState(() {
        _currentPage = current;
        _pages = total;
      });
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
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              widget.title,
              style: const TextStyle(
                color: AppColors.textPrimary,
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
            if (!_hasAccess)
              Text(
                'Purchase this once to view PDF',
                style: TextStyle(
                  fontSize: 11,
                  color: AppColors.primary,
                  fontWeight: FontWeight.w600,
                ),
              )
            else if (_pages > 0)
              Text(
                'Page ${_currentPage + 1} of $_pages',
                style: TextStyle(
                  fontSize: 12,
                  color: AppColors.textSecondary,
                  fontWeight: FontWeight.normal,
                ),
              ),
          ],
        ),
        actions: [
          if (_hasAccess)
            IconButton(
              icon: const Icon(Icons.download, color: AppColors.primary),
              onPressed: _downloadFileToDevice,
              tooltip: 'Download PDF',
            ),
        ],
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
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 32),
                        child: Text(
                          _error!,
                          textAlign: TextAlign.center,
                          style: TextStyle(color: AppColors.textSecondary),
                        ),
                      ),
                    ],
                  ),
                )
              : Stack(
                  fit: StackFit.expand,
                  children: [
                    PDFView(
                      filePath: _localFilePath,
                      enableSwipe: _hasAccess,
                      swipeHorizontal: false,
                      autoSpacing: true,
                      pageFling: _hasAccess,
                      pageSnap: false,
                      defaultPage: 0,
                      fitPolicy: FitPolicy.BOTH,
                      preventLinkNavigation: true,
                      backgroundColor: Colors.grey[200],
                      onRender: (pages) {
                        if (mounted) {
                          setState(() {
                            _pages = pages ?? 0;
                            _isReady = true;
                          });
                        }
                      },
                      onError: (error) {
                        if (mounted) {
                          setState(() => _error = 'Failed to load PDF: $error');
                        }
                      },
                      onPageError: (page, error) {
                        if (mounted) {
                          setState(() => _error = 'Failed to load page $page: $error');
                        }
                      },
                      onViewCreated: (PDFViewController pdfViewController) {
                        _pdfController = pdfViewController;
                        if (!_controller.isCompleted) {
                          _controller.complete(pdfViewController);
                        }
                        Future.delayed(const Duration(milliseconds: 300), () async {
                          await _refreshPageInfo();
                          if (!_hasAccess) {
                            final controller = await _controller.future;
                            controller.setPage(0);
                          }
                        });
                      },
                      onPageChanged: (int? page, int? total) async {
                        if (!mounted) return;
                        final newPage = page ?? _currentPage;
                        if (!_hasAccess && newPage >= _previewPageLimit) {
                          final controller = await _controller.future;
                          controller.setPage(_previewPageLimit - 1);
                          return;
                        }
                        if (!_hasAccess && newPage < 0) {
                          final controller = await _controller.future;
                          controller.setPage(0);
                          return;
                        }
                        setState(() {
                          _currentPage = newPage;
                          _pages = total ?? _pages;
                        });
                      },
                    ),
                    if (!_hasAccess)
                      Positioned(
                        top: 16,
                        left: 16,
                        right: 16,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                          decoration: BoxDecoration(
                            color: AppColors.primary,
                            borderRadius: BorderRadius.circular(12),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.15),
                                blurRadius: 10,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.lock_outline, color: Colors.white, size: 18),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  'Preview: first $_previewPageLimit pages only. Buy to unlock full PDF.',
                                  style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w500),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                  ],
                ),
      bottomNavigationBar: _isReady && _error == null && !_hasAccess
          ? Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: Colors.white,
                border: Border(top: BorderSide(color: Colors.grey.shade200)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 10,
                    offset: const Offset(0, -4),
                  ),
                ],
              ),
              child: SafeArea(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.chevron_left, color: AppColors.textPrimary),
                          onPressed: _currentPage > 0
                              ? () async {
                                  final controller = await _controller.future;
                                  controller.setPage(_currentPage - 1);
                                }
                              : null,
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                          decoration: BoxDecoration(
                            color: AppColors.primary.withOpacity(0.08),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(
                            'Page ${_currentPage + 1} of $_previewPageLimit',
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 13,
                              color: AppColors.primary,
                            ),
                          ),
                        ),
                        IconButton(
                          icon: const Icon(Icons.chevron_right, color: AppColors.textPrimary),
                          onPressed: _currentPage < _previewPageLimit - 1
                              ? () async {
                                  final controller = await _controller.future;
                                  controller.setPage(_currentPage + 1);
                                }
                              : null,
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    SizedBox(
                      height: 48,
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: _goToCheckout,
                        icon: const Icon(Icons.lock_open, color: Colors.white, size: 20),
                        label: Text(
                          'Buy Now - ${AppConfig.currency} ${((widget.material['price'] as num?)?.toDouble() ?? 0.0).toStringAsFixed(0)}',
                          style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.primary,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          elevation: 0,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            )
          : _hasAccess && _isReady && _error == null
          ? Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: Colors.white,
                border: Border(top: BorderSide(color: Colors.grey.shade200)),
              ),
              child: SafeArea(
                child: Row(
                  children: [
                    IconButton(
                      icon: const Icon(Icons.remove, color: AppColors.textPrimary),
                      onPressed: _zoom > 0.5 ? () => _setZoom(_zoom - 0.25) : null,
                    ),
                    Text('${_zoom.toStringAsFixed(1)}x', style: const TextStyle(fontSize: 12)),
                    IconButton(
                      icon: const Icon(Icons.add, color: AppColors.textPrimary),
                      onPressed: _zoom < 4.0 ? () => _setZoom(_zoom + 0.25) : null,
                    ),
                    const Spacer(),
                    IconButton(
                      icon: const Icon(Icons.chevron_left, color: AppColors.textPrimary),
                      onPressed: _currentPage > 0
                          ? () async {
                              final controller = await _controller.future;
                              controller.setPage(_currentPage - 1);
                            }
                          : null,
                    ),
                    GestureDetector(
                      onTap: _jumpToPage,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppColors.primary.withOpacity(0.08),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          'Page ${_currentPage + 1} of $_pages',
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                            color: AppColors.primary,
                          ),
                        ),
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.chevron_right, color: AppColors.textPrimary),
                      onPressed: _currentPage < _pages - 1
                          ? () async {
                              final controller = await _controller.future;
                              controller.setPage(_currentPage + 1);
                            }
                          : null,
                    ),
                  ],
                ),
              ),
            )
          : null,
    );
  }

  Widget _buildPurchasePrompt() {
    final price = (widget.material['price'] as num?)?.toDouble() ?? 0.0;
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.lock_outline, size: 64, color: AppColors.primary.withOpacity(0.5)),
            const SizedBox(height: 16),
            Text(
              'Unlock ${widget.title}',
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
            ),
            const SizedBox(height: 8),
            Text(
              'Buy now for ${AppConfig.currency} ${price.toStringAsFixed(0)} to view and download this PDF.',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 14, color: AppColors.textSecondary),
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: _goToCheckout,
              icon: const Icon(Icons.lock_open),
              label: Text('Buy Now - ${AppConfig.currency} ${price.toStringAsFixed(0)}'),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
