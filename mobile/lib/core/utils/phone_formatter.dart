class PhoneFormatter {
  PhoneFormatter._();

  static String normalize(String phone) {
    String digits = phone.replaceAll(RegExp(r'[^0-9]'), '');
    if (digits.startsWith('0') && digits.length == 10) {
      digits = '255${digits.substring(1)}';
    } else if (digits.startsWith('7') && digits.length == 9) {
      digits = '255$digits';
    } else if (digits.startsWith('+')) {
      digits = digits.replaceFirst('+', '');
    }
    return digits;
  }

  static String display(String phone) {
    final normalized = normalize(phone);
    if (normalized.length == 12 && normalized.startsWith('255')) {
      return '+255 ${normalized.substring(3, 6)} ${normalized.substring(6, 9)} ${normalized.substring(9)}';
    }
    return phone;
  }
}
