import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/config/app_config.dart';
import '../../../core/network/api_client.dart';
import '../../../core/services/dashboard_service.dart';
import 'checkout_screen.dart';

class PaymentsScreen extends StatefulWidget {
  const PaymentsScreen({super.key});

  @override
  State<PaymentsScreen> createState() => _PaymentsScreenState();
}

enum _OrderFilter { all, pending, paid, failed }

class _PaymentsScreenState extends State<PaymentsScreen>
    with SingleTickerProviderStateMixin {
  late final DashboardService _dashboardService;
  late TabController _tabController;
  List<Map<String, dynamic>> _orders = [];
  bool _loading = true;
  _OrderFilter _filter = _OrderFilter.all;

  @override
  void initState() {
    super.initState();
    _dashboardService = DashboardService(ApiClient());
    _tabController = TabController(length: 4, vsync: this);
    _tabController.addListener(() {
      setState(() {
        _filter = _OrderFilter.values[_tabController.index];
      });
    });
    _loadOrders();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadOrders() async {
    setState(() => _loading = true);
    try {
      final response = await _dashboardService.getUserOrders();
      if (mounted) {
        setState(() {
          _orders = List<Map<String, dynamic>>.from(response['data'] ?? []);
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _orders = [];
          _loading = false;
        });
      }
    }
  }

  List<Map<String, dynamic>> get _filteredOrders {
    switch (_filter) {
      case _OrderFilter.pending:
        return _orders.where((o) => o['status'] == 'pending').toList();
      case _OrderFilter.paid:
        return _orders.where((o) => o['status'] == 'paid').toList();
      case _OrderFilter.failed:
        return _orders
            .where((o) => o['status'] == 'failed' || o['status'] == 'expired')
            .toList();
      case _OrderFilter.all:
      default:
        return _orders;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(),
            _buildTabs(),
            Expanded(
              child: _loading
                  ? const Center(
                      child: CircularProgressIndicator(color: AppColors.primary))
                  : RefreshIndicator(
                      onRefresh: _loadOrders,
                      color: AppColors.primary,
                      child: _filteredOrders.isEmpty
                          ? _buildEmptyState()
                          : ListView.builder(
                              padding: const EdgeInsets.all(16),
                              itemCount: _filteredOrders.length,
                              itemBuilder: (context, index) {
                                return _buildOrderCard(_filteredOrders[index]);
                              },
                            ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF024938), Color(0xFF047857)],
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              IconButton(
                icon: const Icon(Icons.arrow_back, color: Colors.white),
                onPressed: () => context.pop(),
              ),
              const SizedBox(width: 8),
              const Text(
                'My Orders',
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildStatCard('Total', _orders.length.toString()),
              _buildStatCard(
                'Pending',
                _orders.where((o) => o['status'] == 'pending').length.toString(),
              ),
              _buildStatCard(
                'Paid',
                _orders.where((o) => o['status'] == 'paid').length.toString(),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(String label, String value) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.12),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        children: [
          Text(
            value,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: TextStyle(
              fontSize: 11,
              color: Colors.white.withOpacity(0.8),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTabs() {
    return Container(
      color: AppColors.primary,
      child: TabBar(
        controller: _tabController,
        indicatorColor: Colors.white,
        indicatorWeight: 3,
        labelColor: Colors.white,
        unselectedLabelColor: Colors.white70,
        labelStyle: const TextStyle(fontWeight: FontWeight.bold),
        tabs: const [
          Tab(text: 'All'),
          Tab(text: 'Pending'),
          Tab(text: 'Paid'),
          Tab(text: 'Failed'),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    String message;
    switch (_filter) {
      case _OrderFilter.pending:
        message = 'Hakuna malipo yanayosubiri';
        break;
      case _OrderFilter.paid:
        message = 'Bado hujalipa chochote';
        break;
      case _OrderFilter.failed:
        message = 'Hakuna malipo yaliyoshindwa';
        break;
      case _OrderFilter.all:
      default:
        message = 'Hakuna maagizo bado';
    }
    return ListView(
      children: [
        SizedBox(height: MediaQuery.of(context).size.height * 0.25),
        Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                Icons.receipt_long_outlined,
                size: 72,
                color: Colors.grey.shade400,
              ),
              const SizedBox(height: 16),
              Text(
                message,
                style: TextStyle(fontSize: 16, color: Colors.grey.shade600),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildOrderCard(Map<String, dynamic> order) {
    final amount = (order['total_amount'] as num?)?.toDouble() ?? 0.0;
    final status = order['status']?.toString() ?? 'pending';
    final reference = order['reference']?.toString() ?? '';
    final createdAt = order['created_at']?.toString() ?? '';
    final items = List<Map<String, dynamic>>.from(order['items'] ?? []);
    final firstItem = items.isNotEmpty ? items.first : null;
    final note = firstItem?['note'] as Map<String, dynamic>?;
    final title = note?['title']?.toString() ?? 'Material';
    final type = note?['type']?.toString() ?? 'notes';

    final statusInfo = _statusInfo(status);

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        title,
                        style: const TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textPrimary,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 4),
                      Text(
                        type.toUpperCase(),
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 12),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                  decoration: BoxDecoration(
                    color: statusInfo.color.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: statusInfo.color, width: 1),
                  ),
                  child: Text(
                    statusInfo.text,
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: statusInfo.color,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 14),
            _buildInfoRow(Icons.receipt_long_outlined, 'Ref: $reference'),
            const SizedBox(height: 6),
            _buildInfoRow(Icons.calendar_today_outlined, _formatDate(createdAt)),
            const SizedBox(height: 6),
            _buildInfoRow(
              Icons.shopping_bag_outlined,
              '${items.length} ${items.length == 1 ? 'item' : 'items'}',
            ),
            const Divider(height: 24),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Total',
                  style: TextStyle(fontSize: 14, color: AppColors.textSecondary),
                ),
                Text(
                  '${AppConfig.currency} ${amount.toStringAsFixed(0)}',
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: AppColors.primary,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 14),
            _buildActionButtons(order, note, status),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, size: 16, color: AppColors.textSecondary),
        const SizedBox(width: 8),
        Text(
          text,
          style: TextStyle(fontSize: 13, color: AppColors.textSecondary),
        ),
      ],
    );
  }

  Widget _buildActionButtons(
    Map<String, dynamic> order,
    Map<String, dynamic>? note,
    String status,
  ) {
    final statusLower = status.toLowerCase();

    if (statusLower == 'paid') {
      final noteId = note?['id'] as int? ?? 0;
      final type = note?['type']?.toString() ?? 'notes';
      final title = note?['title']?.toString() ?? 'Material';
      final downloadUrl = note?['download_url']?.toString() ??
          '${AppConfig.baseUrl}/catalog/materials/$type/$noteId/download';

      return Row(
        children: [
          Expanded(
            child: OutlinedButton.icon(
              onPressed: () => _viewMaterial(type, noteId, title),
              icon: const Icon(Icons.visibility, size: 18),
              label: const Text('Tazama'),
              style: OutlinedButton.styleFrom(
                foregroundColor: AppColors.primary,
                side: BorderSide(color: AppColors.primary),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                padding: const EdgeInsets.symmetric(vertical: 12),
              ),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: ElevatedButton.icon(
              onPressed: () => _downloadFile(downloadUrl, title),
              icon: const Icon(Icons.download, size: 18),
              label: const Text('Pakua'),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                padding: const EdgeInsets.symmetric(vertical: 12),
                elevation: 0,
              ),
            ),
          ),
        ],
      );
    }

    if (statusLower == 'pending' || statusLower == 'failed') {
      return SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
          onPressed: () => _retryPayment(order, note),
          icon: const Icon(Icons.payment, size: 18),
          label: const Text(
            'Lipa Sasa',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
          ),
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.primary,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            padding: const EdgeInsets.symmetric(vertical: 14),
            elevation: 0,
          ),
        ),
      );
    }

    return const SizedBox.shrink();
  }

  ({Color color, String text}) _statusInfo(String status) {
    switch (status.toLowerCase()) {
      case 'paid':
        return (color: AppColors.success, text: 'Imelipwa');
      case 'pending':
        return (color: AppColors.warning, text: 'Inasubiri');
      case 'failed':
        return (color: AppColors.error, text: 'Imeshindwa');
      case 'expired':
        return (color: Colors.orange, text: 'Muda Umeisha');
      default:
        return (color: AppColors.textMuted, text: status);
    }
  }

  void _retryPayment(Map<String, dynamic> order, Map<String, dynamic>? note) {
    final amount = (order['total_amount'] as num?)?.toDouble() ?? 0.0;
    final type = note?['type']?.toString() ?? 'notes';
    final id = note?['id'] as int? ?? 0;
    final title = note?['title']?.toString() ?? 'Material';

    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => CheckoutScreen(
          payload: {
            'type': type,
            'id': id,
            'title': title,
            'price': amount,
          },
        ),
      ),
    );
  }

  void _viewMaterial(String type, int id, String title) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Opening $title...')),
    );
  }

  void _downloadFile(String url, String title) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Downloading $title...')),
    );
  }

  String _formatDate(String dateString) {
    if (dateString.isEmpty) return '';
    try {
      final date = DateTime.parse(dateString);
      return '${date.day}/${date.month}/${date.year}';
    } catch (e) {
      return dateString;
    }
  }
}
