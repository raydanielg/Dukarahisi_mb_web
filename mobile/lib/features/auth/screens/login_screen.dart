import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/validators.dart';
import '../../../core/utils/phone_formatter.dart';
import '../../../core/widgets/custom_toast.dart';
import '../../../core/services/auth_service.dart';
import '../../../core/network/api_client.dart';
import '../../../core/network/api_exceptions.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailOrPhoneController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;
  bool _loading = false;
  late final AuthService _authService;

  @override
  void initState() {
    super.initState();
    _authService = AuthService(ApiClient());
  }

  @override
  void dispose() {
    _emailOrPhoneController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  void _login() async {
    if (_formKey.currentState?.validate() ?? false) {
      setState(() => _loading = true);
      
      try {
        final response = await _authService.login(
          phoneNumber: _emailOrPhoneController.text.trim(),
          password: _passwordController.text,
        );
        
        if (mounted) {
          setState(() => _loading = false);
          
          if (response['status'] == 'success') {
            CustomToast.show(
              context,
              message: 'Welcome back! 🎉',
              type: ToastType.success,
            );
            Future.delayed(const Duration(milliseconds: 500), () {
              if (mounted) context.go('/main');
            });
          } else {
            _showErrorDialog(
              title: 'Login Failed',
              message: response['message'] ?? 'Invalid email, phone number or password.',
            );
          }
        }
      } on NetworkException catch (e) {
        if (mounted) {
          setState(() => _loading = false);
          _showErrorDialog(
            title: 'Connection Error',
            message: e.message,
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
          _showErrorDialog(
            title: 'Validation Error',
            message: errorMessage,
          );
        }
      } on ApiException catch (e) {
        if (mounted) {
          setState(() => _loading = false);
          _showErrorDialog(
            title: 'Login Failed',
            message: e.message.isNotEmpty ? e.message : 'Invalid email, phone number or password.',
          );
        }
      } catch (e) {
        if (mounted) {
          setState(() => _loading = false);
          _showErrorDialog(
            title: 'Server Error',
            message: 'Something went wrong on our end. Please try again later.',
          );
        }
      }
    } else {
      CustomToast.show(
        context,
        message: 'Please fill in all fields correctly',
        type: ToastType.error,
      );
    }
  }

  void _showErrorDialog({required String title, required String message}) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: AppColors.error.withOpacity(0.1),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(Icons.error_outline, color: AppColors.error, size: 24),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                title,
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
            ),
          ],
        ),
        content: Text(
          message,
          style: TextStyle(
            fontSize: 14,
            color: AppColors.textSecondary,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: Text(
              'Try Again',
              style: TextStyle(
                color: AppColors.primary,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
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
              // Header matching web auth
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
                      child: Image.asset('assets/images/login.png', fit: BoxFit.contain),
                    ),
                    const SizedBox(height: 24),
                    Text(
                      'Welcome Back',
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                            fontSize: 24,
                          ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Sign in to continue to Dukarahisi',
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                            color: Colors.white70,
                            fontSize: 14,
                          ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 40),
              // White card body matching web auth
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
                    padding: const EdgeInsets.fromLTRB(28, 32, 28, 28),
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          TextFormField(
                            controller: _emailOrPhoneController,
                            keyboardType: TextInputType.emailAddress,
                            decoration: const InputDecoration(
                              labelText: 'Email or Phone',
                              hintText: 'Enter email or phone number',
                              prefixIcon: Icon(Icons.person_outline),
                            ),
                            validator: Validators.emailOrPhone,
                          ),
                          const SizedBox(height: 18),
                          TextFormField(
                            controller: _passwordController,
                            obscureText: _obscurePassword,
                            decoration: InputDecoration(
                              labelText: 'Password',
                              hintText: 'Enter your password',
                              prefixIcon: const Icon(Icons.lock_outline),
                              suffixIcon: IconButton(
                                icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility),
                                onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                              ),
                            ),
                            validator: Validators.password,
                          ),
                          const SizedBox(height: 16),
                          Align(
                            alignment: Alignment.centerRight,
                            child: TextButton(
                              onPressed: () => context.push('/forgot-password'),
                              style: TextButton.styleFrom(
                                foregroundColor: AppColors.primary,
                              ),
                              child: const Text(
                                'Forgot Password?',
                                style: TextStyle(
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(height: 28),
                          ElevatedButton(
                            onPressed: _loading ? null : _login,
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
                                    'Login',
                                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                                  ),
                          ),
                          const SizedBox(height: 24),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Text("Don't have an account?", style: TextStyle(color: AppColors.textSecondary)),
                              TextButton(
                                onPressed: () => context.push('/register'),
                                child: const Text('Register'),
                              ),
                            ],
                          ),
                        ],
                      ),
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
