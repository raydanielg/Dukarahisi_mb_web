import 'dart:async';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/config/app_config.dart';
import '../../../core/network/api_client.dart';
import '../../../core/services/payment_service.dart';

class CheckoutScreen extends StatefulWidget {
  final Map<String, dynamic> payload;

  const CheckoutScreen({super.key, required this.payload});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

enum _CheckoutStatus { idle, creatingOrder, awaitingPayment, processing, success, failed }

class _CheckoutScreenState extends State<CheckoutScreen> {
  late final PaymentService _paymentService;
  final _phoneController = TextEditingController();
  _CheckoutStatus _status = _CheckoutStatus.idle;
  String? _error;
  int? _orderId;
  Timer? _pollingTimer;
  int _pollCount = 0;

  final String _countryCode = '255';

  @override
  void initState() {
    super.initState();
    _paymentService = PaymentService(ApiClient());
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    _phoneController.dispose();
    super.dispose();
  }

  String get _formattedPhone {
    final input = _phoneController.text.replaceAll(RegExp(r'\D'), '');
    if (input.startsWith('0')) {
      return '$_countryCode${input.substring(1)}';
    }
    if (input.startsWith(_countryCode)) {
      return input;
    }
    return '$_countryCode$input';
  }

  Future<void> _pay() async {
    final phone = _formattedPhone;
    if (phone.length < 12) {
      setState(() => _error = 'Enter a valid phone number');
      return;
    }

    setState(() {
      _status = _CheckoutStatus.creatingOrder;
      _error = null;
    });

    try {
      final type = widget.payload['type']?.toString() ?? 'notes';
      final id = widget.payload['id'] as int? ?? 0;

      final orderResponse = await _paymentService.createSingleOrder(
        materialType: type,
        materialId: id,
      );

      final orderData = orderResponse['data'] as Map<String, dynamic>?;
      _orderId = orderData?['order_id'] as int?;

      if (_orderId == null) {
        throw Exception('Failed to create order');
      }

      setState(() => _status = _CheckoutStatus.awaitingPayment);

      final paymentResponse = await _paymentService.initiatePayment(
        orderId: _orderId!,
        phoneNumber: phone,
      );

      if (paymentResponse['status'] != 'success') {
        throw Exception(paymentResponse['message']?.toString() ?? 'Payment initiation failed');
      }

      setState(() => _status = _CheckoutStatus.processing);
      _startPolling();
    } catch (e) {
      if (mounted) {
        setState(() {
          _status = _CheckoutStatus.failed;
          _error = e.toString();
        });
      }
    }
  }

  void _startPolling() {
    _pollCount = 0;
    _pollingTimer?.cancel();
    _pollingTimer = Timer.periodic(
      Duration(seconds: AppConfig.paymentPollingIntervalSeconds),
      (timer) async {
        if (_orderId == null || !mounted) {
          timer.cancel();
          return;
        }
        if (_pollCount >= AppConfig.paymentPollingMaxAttempts) {
          timer.cancel();
          if (mounted) {
            setState(() {
              _status = _CheckoutStatus.failed;
              _error = 'Payment timeout. Please try again.';
            });
          }
          return;
        }
        _pollCount++;
        try {
          final response = await _paymentService.checkStatus(_orderId!);
          final data = response['data'] as Map<String, dynamic>?;
          final paymentStatus = data?['payment_status']?.toString().toLowerCase();
          if (paymentStatus == 'success' || paymentStatus == 'paid') {
            timer.cancel();
            if (mounted) {
              setState(() => _status = _CheckoutStatus.success);
            }
          } else if (paymentStatus == 'failed') {
            timer.cancel();
            if (mounted) {
              setState(() {
                _status = _CheckoutStatus.failed;
                _error = 'Payment failed. Please try again.';
              });
            }
          }
        } catch (_) {
          // ignore polling errors
        }
      },
    );
  }

  void _retry() {
    setState(() {
      _status = _CheckoutStatus.idle;
      _error = null;
    });
  }

  void _goBack() {
    context.pop();
  }

  void _download() {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Download started')),
    );
  }

  @override
  Widget build(BuildContext context) {
    final title = widget.payload['title']?.toString() ?? 'Material';
    final type = widget.payload['type']?.toString() ?? 'notes';
    final price = (widget.payload['price'] as num?)?.toDouble() ?? 0.0;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: SafeArea(
        child: Column(
          children: [
            _buildAppBar(),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    _buildMaterialCard(title, type, price),
                    const SizedBox(height: 24),
                    if (_status == _CheckoutStatus.success)
                      _buildSuccessView(price)
                    else if (_status == _CheckoutStatus.failed)
                      _buildFailedView()
                    else
                      _buildPaymentForm(price),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAppBar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 12,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          IconButton(
            icon: const Icon(Icons.arrow_back, color: AppColors.textPrimary),
            onPressed: _goBack,
          ),
          const SizedBox(width: 8),
          const Text(
            'Checkout',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMaterialCard(String title, String type, double price) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF024938), Color(0xFF047857)],
        ),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withOpacity(0.3),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              type.toUpperCase(),
              style: const TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
          ),
          const SizedBox(height: 16),
          Text(
            title,
            style: const TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 20),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Total',
                style: TextStyle(fontSize: 14, color: Colors.white70),
              ),
              Text(
                '${AppConfig.currency} ${price.toStringAsFixed(0)}',
                style: const TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentForm(double price) {
    final isBusy = _status == _CheckoutStatus.creatingOrder ||
        _status == _CheckoutStatus.awaitingPayment ||
        _status == _CheckoutStatus.processing;

    String statusText;
    switch (_status) {
      case _CheckoutStatus.creatingOrder:
        statusText = 'Creating order...';
        break;
      case _CheckoutStatus.awaitingPayment:
        statusText = 'Initiating payment...';
        break;
      case _CheckoutStatus.processing:
        statusText = 'Waiting for payment confirmation...';
        break;
      default:
        statusText = '';
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.grey.shade200),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Mobile Money Number',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              Container(
                decoration: BoxDecoration(
                  color: Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: _error != null ? Colors.red : Colors.grey.shade300),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                      decoration: BoxDecoration(
                        color: AppColors.primary.withOpacity(0.08),
                        borderRadius: const BorderRadius.horizontal(left: Radius.circular(16)),
                      ),
                      child: const Text(
                        '+255',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary,
                        ),
                      ),
                    ),
                    Expanded(
                      child: TextField(
                        controller: _phoneController,
                        keyboardType: TextInputType.phone,
                        enabled: !isBusy,
                        decoration: const InputDecoration(
                          hintText: '6XX XXX XXX',
                          border: InputBorder.none,
                          contentPadding: EdgeInsets.symmetric(horizontal: 16),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              if (_error != null)
                Padding(
                  padding: const EdgeInsets.only(top: 8),
                  child: Text(
                    _error!,
                    style: const TextStyle(color: Colors.red, fontSize: 12),
                  ),
                ),
              const SizedBox(height: 12),
              Text(
                'Supports M-Pesa, Tigo Pesa, Airtel Money',
                style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
              ),
            ],
          ),
        ),
        const SizedBox(height: 24),
        if (isBusy)
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: Column(
              children: [
                const CircularProgressIndicator(color: AppColors.primary),
                const SizedBox(height: 16),
                Text(
                  statusText,
                  textAlign: TextAlign.center,
                  style: TextStyle(color: AppColors.textSecondary),
                ),
              ],
            ),
          )
        else
          SizedBox(
            height: 56,
            child: ElevatedButton(
              onPressed: _pay,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                elevation: 0,
              ),
              child: Text(
                'Pay ${AppConfig.currency} ${price.toStringAsFixed(0)}',
                style: const TextStyle(fontSize: 17, fontWeight: FontWeight.bold),
              ),
            ),
          ),
      ],
    );
  }

  Widget _buildSuccessView(double price) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: Colors.green.shade50,
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.check, size: 40, color: Colors.green.shade600),
          ),
          const SizedBox(height: 20),
          const Text(
            'Payment Successful',
            style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          Text(
            '${AppConfig.currency} ${price.toStringAsFixed(0)} paid successfully.',
            textAlign: TextAlign.center,
            style: TextStyle(color: AppColors.textSecondary),
          ),
          const SizedBox(height: 24),
          SizedBox(
            height: 52,
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: _download,
              icon: const Icon(Icons.download, color: Colors.white),
              label: const Text(
                'Download PDF',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                elevation: 0,
              ),
            ),
          ),
          const SizedBox(height: 12),
          SizedBox(
            height: 48,
            width: double.infinity,
            child: OutlinedButton(
              onPressed: _goBack,
              style: OutlinedButton.styleFrom(
                foregroundColor: AppColors.textPrimary,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                side: BorderSide(color: Colors.grey.shade300),
              ),
              child: const Text('Back to Materials'),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFailedView() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: Colors.red.shade50,
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.close, size: 40, color: Colors.red.shade600),
          ),
          const SizedBox(height: 20),
          const Text(
            'Payment Failed',
            style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          Text(
            _error ?? 'Something went wrong. Please try again.',
            textAlign: TextAlign.center,
            style: TextStyle(color: AppColors.textSecondary),
          ),
          const SizedBox(height: 24),
          SizedBox(
            height: 52,
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: _retry,
              icon: const Icon(Icons.refresh, color: Colors.white),
              label: const Text(
                'Try Again',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                elevation: 0,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
