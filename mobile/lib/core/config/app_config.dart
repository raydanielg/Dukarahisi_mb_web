import 'package:flutter_dotenv/flutter_dotenv.dart';

class AppConfig {
  static String get baseUrl => dotenv.env['API_BASE_URL'] ?? 'https://app.darasahurutz.com/api';
  static String get appName => 'Dukarahisi';
  static String get appVersion => '1.0.0';
  static String get currency => 'TZS';
  static bool get enableLogging => dotenv.env['ENABLE_LOGGING']?.toLowerCase() == 'true';
  static int get otpLength => 6;
  static int get paymentPollingIntervalSeconds => 5;
  static int get paymentPollingMaxAttempts => 60;
}
