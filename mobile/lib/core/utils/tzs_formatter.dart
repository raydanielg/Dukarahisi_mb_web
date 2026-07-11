import 'package:intl/intl.dart';

class TzsFormatter {
  TzsFormatter._();

  static String format(num amount) {
    final formatter = NumberFormat.currency(
      locale: 'sw_TZ',
      symbol: 'TSh ',
      decimalDigits: 0,
    );
    return formatter.format(amount);
  }

  static String compact(num amount) {
    final formatter = NumberFormat.compactCurrency(
      locale: 'sw_TZ',
      symbol: 'TSh ',
      decimalDigits: 0,
    );
    return formatter.format(amount);
  }
}
