import 'package:equatable/equatable.dart';

class Payout extends Equatable {
  final String id;
  final double amount;
  final String currency;
  final DateTime date;
  final String status;
  final String reference;

  const Payout({
    required this.id,
    required this.amount,
    required this.currency,
    required this.date,
    required this.status,
    required this.reference,
  });

  factory Payout.fromJson(Map<String, dynamic> json) {
    return Payout(
      id: json['id'],
      amount: double.parse(json['amount'].toString()),
      currency: json['currency'] ?? 'GHS',
      date: DateTime.parse(json['created_at']),
      status: json['status'],
      reference: json['reference'],
    );
  }

  @override
  List<Object?> get props => [id, amount, date, status, reference];
}

class EarningsSummary extends Equatable {
  final double dailyTotal;
  final double weeklyTotal;
  final double monthlyTotal;
  final List<Payout> recentPayouts;

  const EarningsSummary({
    required this.dailyTotal,
    required this.weeklyTotal,
    required this.monthlyTotal,
    required this.recentPayouts,
  });

  @override
  List<Object?> get props => [dailyTotal, weeklyTotal, monthlyTotal, recentPayouts];
}
