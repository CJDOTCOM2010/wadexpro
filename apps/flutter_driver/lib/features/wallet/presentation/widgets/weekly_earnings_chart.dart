import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';
import 'package:intl/intl.dart';

class WeeklyEarningsChart extends StatelessWidget {
  final Map<String, dynamic>? stats;

  const WeeklyEarningsChart({super.key, this.stats});

  @override
  Widget build(BuildContext context) {
    if (stats == null || (stats!['days'] as List).isEmpty) {
      return const SizedBox(
        height: 200,
        child: Center(child: Text('No earnings data for this week.', style: TextStyle(color: Colors.white54))),
      );
    }

    final days = stats!['days'] as List;
    final currency = stats!['currency'] ?? 'GHS';

    return Container(
      height: 220,
      padding: const EdgeInsets.fromLTRB(10, 20, 10, 10),
      child: BarChart(
        BarChartData(
          alignment: BarChartAlignment.spaceAround,
          maxY: _getMaxY(days),
          barTouchData: BarTouchData(
            touchTooltipData: BarTouchTooltipData(
              // getTooltipColor: (group) => AppColors.accent.withOpacity(0.9),
              getTooltipItem: (group, groupIndex, rod, rodIndex) {
                return BarTooltipItem(
                  '$currency ${rod.toY.toStringAsFixed(2)}',
                  const TextStyle(color: AppColors.primaryNavy, fontWeight: FontWeight.bold),
                );
              },
            ),
          ),
          titlesData: FlTitlesData(
            show: true,
            bottomTitles: AxisTitles(
              sideTitles: SideTitles(
                showTitles: true,
                getTitlesWidget: (value, meta) {
                  if (value.toInt() >= days.length) return const SizedBox.shrink();
                  final dateStr = days[value.toInt()]['date'];
                  final date = DateTime.parse(dateStr);
                  return Padding(
                    padding: const EdgeInsets.only(top: 8.0),
                    child: Text(
                      DateFormat('E').format(date).toUpperCase(),
                      style: const TextStyle(color: Colors.white60, fontSize: 10, fontWeight: FontWeight.bold),
                    ),
                  );
                },
                reservedSize: 30,
              ),
            ),
            leftTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
          ),
          gridData: const FlGridData(show: false),
          borderData: FlBorderData(show: false),
          barGroups: days.asMap().entries.map((entry) {
            return BarChartGroupData(
              x: entry.key,
              barRods: [
                BarChartRodData(
                  toY: (entry.value['total_earned'] as num).toDouble(),
                  color: AppColors.accent,
                  width: 14,
                  borderRadius: BorderRadius.circular(4),
                  backDrawRodData: BackgroundBarChartRodData(
                    show: true,
                    toY: _getMaxY(days),
                    color: Colors.white.withOpacity(0.05),
                  ),
                ),
              ],
            );
          }).toList(),
        ),
      ),
    );
  }

  double _getMaxY(List days) {
    double max = 0;
    for (var day in days) {
      if ((day['total_earned'] as num).toDouble() > max) {
        max = (day['total_earned'] as num).toDouble();
      }
    }
    return max == 0 ? 100 : max * 1.2;
  }
}
