import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/services/auth_service.dart';
import '../../../core/network/api_client.dart';
import '../../../core/network/api_exceptions.dart';
import '../../../core/widgets/custom_toast.dart';

class OTPVerificationScreen extends StatefulWidget {
  const OTPVerificationScreen({super.key});

  @override
  State<OTPVerificationScreen> createState() => _OTPVerificationScreenState();
}

class _OTPVerificationScreenState extends State<OTPVerificationScreen>
    with TickerProviderStateMixin {
  final List<TextEditingController> _controllers = List.generate(6, (_) => TextEditingController());
  final List<FocusNode> _focusNodes = List.generate(6, (_) => FocusNode());
  bool _loading = false;
  int _countdown = 60;
  late AnimationController _countdownController;
  late Animation<int> _countdownAnimation;
  late final AuthService _authService;
  String? _phoneNumber;

  @override
  void initState() {
    super.initState();
    _authService = AuthService(ApiClient());
    
    // Get phone number from route extra
    _phoneNumber = ModalRoute.of(context)?.settings.arguments as String?;
    
    _countdownController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 60),
    );
    _countdownAnimation = IntTween(begin: 60, end: 0).animate(_countdownController)
      ..addListener(() {
        setState(() {
          _countdown = _countdownAnimation.value;
        });
      });
    _countdownController.forward();
  }

  @override
  void dispose() {
    for (var controller in _controllers) {
      controller.dispose();
    }
    for (var node in _focusNodes) {
      node.dispose();
    }
    _countdownController.dispose();
    super.dispose();
  }

  void _onOTPChanged(int index, String value) {
    if (value.isNotEmpty && index < 5) {
      _focusNodes[index + 1].requestFocus();
    }
    if (value.isEmpty && index > 0) {
      _focusNodes[index - 1].requestFocus();
    }
  }

  void _verifyOTP() async {
    final otp = _controllers.map((c) => c.text).join();
    if (otp.length == 6) {
      if (_phoneNumber == null || _phoneNumber!.isEmpty) {
        CustomToast.show(
          context,
          message: 'Phone number not provided',
          type: ToastType.error,
        );
        return;
      }
      
      setState(() => _loading = true);
      
      try {
        final response = await _authService.verifyOtp(
          phoneNumber: _phoneNumber!,
          otpCode: otp,
        );
        
        if (mounted) {
          setState(() => _loading = false);
          
          if (response['status'] == 'success') {
            CustomToast.show(
              context,
              message: response['message'] ?? 'OTP verified successfully!',
              type: ToastType.success,
            );
            // Navigate to home after successful verification
            Future.delayed(const Duration(milliseconds: 500), () {
              if (mounted) context.go('/home');
            });
          } else {
            CustomToast.show(
              context,
              message: response['message'] ?? 'OTP verification failed',
              type: ToastType.error,
            );
          }
        }
      } on NetworkException catch (e) {
        if (mounted) {
          setState(() => _loading = false);
          CustomToast.show(
            context,
            message: e.message,
            type: ToastType.error,
          );
        }
      } on ValidationException catch (e) {
        if (mounted) {
          setState(() => _loading = false);
          String errorMessage = e.message;
          if (e.errors.isNotEmpty) {
            final firstError = e.errors.values.first;
            if (firstError is List && firstError.isNotEmpty) {
              errorMessage = firstError.first.toString();
            } else if (firstError is String) {
              errorMessage = firstError;
            }
          }
          CustomToast.show(
            context,
            message: errorMessage,
            type: ToastType.error,
          );
        }
      } on ApiException catch (e) {
        if (mounted) {
          setState(() => _loading = false);
          CustomToast.show(
            context,
            message: e.message,
            type: ToastType.error,
          );
        }
      } catch (e) {
        if (mounted) {
          setState(() => _loading = false);
          CustomToast.show(
            context,
            message: 'An unexpected error occurred',
            type: ToastType.error,
          );
        }
      }
    } else {
      CustomToast.show(
        context,
        message: 'Please enter the complete 6-digit OTP',
        type: ToastType.error,
      );
    }
  }

  void _resendOTP() async {
    if (_countdown == 0) {
      if (_phoneNumber == null || _phoneNumber!.isEmpty) {
        CustomToast.show(
          context,
          message: 'Phone number not provided',
          type: ToastType.error,
        );
        return;
      }
      
      try {
        final response = await _authService.forgotPassword(
          phoneNumber: _phoneNumber!,
        );
        
        if (response['status'] == 'success') {
          setState(() {
            _countdown = 60;
          });
          _countdownController.reset();
          _countdownController.forward();
          CustomToast.show(
            context,
            message: response['message'] ?? 'OTP resent successfully!',
            type: ToastType.success,
          );
        } else {
          CustomToast.show(
            context,
            message: response['message'] ?? 'Failed to resend OTP',
            type: ToastType.error,
          );
        }
      } catch (e) {
        CustomToast.show(
          context,
          message: 'Failed to resend OTP',
          type: ToastType.error,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [AppColors.primary, AppColors.primaryDark, Color(0xFF065F46)],
          ),
        ),
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const SizedBox(height: 40),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Column(
                  children: [
                    Container(
                      width: 80,
                      height: 80,
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.15),
                        borderRadius: BorderRadius.circular(24),
                        border: Border.all(color: Colors.white.withOpacity(0.25), width: 1.5),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 20,
                            offset: const Offset(0, 10),
                          ),
                        ],
                      ),
                      child: Image.asset('assets/images/verfyottp.png', fit: BoxFit.contain),
                    ),
                    const SizedBox(height: 20),
                    Text(
                      'Verify OTP',
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'Enter the 6-digit code sent to your phone',
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.white70),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 32),
              Expanded(
                child: Container(
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.only(
                      topLeft: Radius.circular(32),
                      topRight: Radius.circular(32),
                    ),
                  ),
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(28),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: List.generate(6, (index) {
                            return SizedBox(
                              width: 50,
                              height: 60,
                              child: TextField(
                                controller: _controllers[index],
                                focusNode: _focusNodes[index],
                                keyboardType: TextInputType.number,
                                textAlign: TextAlign.center,
                                style: const TextStyle(
                                  fontSize: 24,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: 0,
                                ),
                                inputFormatters: [
                                  FilteringTextInputFormatter.digitsOnly,
                                  LengthLimitingTextInputFormatter(1),
                                ],
                                decoration: InputDecoration(
                                  filled: true,
                                  fillColor: AppColors.surface,
                                  border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    borderSide: const BorderSide(color: AppColors.border),
                                  ),
                                  enabledBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    borderSide: const BorderSide(color: AppColors.border),
                                  ),
                                  focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    borderSide: const BorderSide(color: Color(0xFF10B981), width: 2),
                                  ),
                                  counterText: '',
                                ),
                                onChanged: (value) => _onOTPChanged(index, value),
                              ),
                            );
                          }),
                        ),
                        const SizedBox(height: 32),
                        ElevatedButton(
                          onPressed: _loading ? null : _verifyOTP,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(10),
                            ),
                            elevation: 2,
                          ),
                          child: _loading
                              ? const SizedBox(
                                  height: 22,
                                  width: 22,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2.5,
                                    valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                  ),
                                )
                              : const Text(
                                  'Verify OTP',
                                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                                ),
                        ),
                        const SizedBox(height: 24),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              "Didn't receive the code? ",
                              style: TextStyle(color: AppColors.textSecondary),
                            ),
                            TextButton(
                              onPressed: _countdown == 0 ? _resendOTP : null,
                              child: Text(
                                _countdown > 0 ? 'Resend in $_countdown s' : 'Resend',
                                style: TextStyle(
                                  color: _countdown > 0 ? AppColors.textMuted : AppColors.primary,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        TextButton(
                          onPressed: () => context.pop(),
                          child: const Text('Back'),
                        ),
                      ],
                    ),
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
