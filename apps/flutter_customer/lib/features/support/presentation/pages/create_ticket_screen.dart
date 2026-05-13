import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/support_provider.dart';

class CreateTicketScreen extends ConsumerStatefulWidget {
  const CreateTicketScreen({super.key});

  @override
  ConsumerState<CreateTicketScreen> createState() => _CreateTicketScreenState();
}

class _CreateTicketScreenState extends ConsumerState<CreateTicketScreen> {
  final _formKey = GlobalKey<FormState>();
  final _subjectController = TextEditingController();
  final _messageController = TextEditingController();
  
  String _selectedCategory = 'general';
  String _selectedPriority = 'low';
  bool _isLoading = false;

  final _categories = [
    {'value': 'general', 'label': 'General Inquiry'},
    {'value': 'billing', 'label': 'Billing & Payments'},
    {'value': 'lost_item', 'label': 'Lost Item'},
    {'value': 'safety', 'label': 'Safety & Security'},
    {'value': 'technical', 'label': 'Technical Issue'},
  ];

  final _priorities = [
    {'value': 'low', 'label': 'Low Priority'},
    {'value': 'medium', 'label': 'Medium Priority'},
    {'value': 'high', 'label': 'High Priority'},
    {'value': 'urgent', 'label': 'Urgent'},
  ];

  void _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    try {
      await ref.read(supportTicketsProvider.notifier).createTicket(
        _subjectController.text.trim(),
        _selectedCategory,
        _selectedPriority,
        _messageController.text.trim(),
      );

      if (mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Support ticket created successfully!')),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString())),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('New Support Ticket', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: AppColors.primaryNavy,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Tell us how we can help',
                style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: AppColors.primaryNavy),
              ),
              const SizedBox(height: 8),
              const Text(
                'Please provide as much detail as possible so our support team can assist you efficiently.',
                style: TextStyle(color: Colors.black54),
              ),
              const SizedBox(height: 24),

              // Category
              const Text('Category', style: TextStyle(fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
              const SizedBox(height: 8),
              DropdownButtonFormField<String>(
                value: _selectedCategory,
                decoration: _inputDecoration(),
                items: _categories.map((c) => DropdownMenuItem(
                  value: c['value'],
                  child: Text(c['label']!),
                )).toList(),
                onChanged: (val) => setState(() => _selectedCategory = val!),
              ),
              const SizedBox(height: 20),

              // Priority
              const Text('Priority', style: TextStyle(fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
              const SizedBox(height: 8),
              DropdownButtonFormField<String>(
                value: _selectedPriority,
                decoration: _inputDecoration(),
                items: _priorities.map((p) => DropdownMenuItem(
                  value: p['value'],
                  child: Text(p['label']!),
                )).toList(),
                onChanged: (val) => setState(() => _selectedPriority = val!),
              ),
              const SizedBox(height: 20),

              // Subject
              const Text('Subject', style: TextStyle(fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
              const SizedBox(height: 8),
              TextFormField(
                controller: _subjectController,
                decoration: _inputDecoration().copyWith(hintText: 'Brief summary of the issue'),
                validator: (val) => val == null || val.isEmpty ? 'Please enter a subject' : null,
              ),
              const SizedBox(height: 20),

              // Message
              const Text('Message / Details', style: TextStyle(fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
              const SizedBox(height: 8),
              TextFormField(
                controller: _messageController,
                maxLines: 6,
                decoration: _inputDecoration().copyWith(hintText: 'Describe the issue in detail...'),
                validator: (val) => val == null || val.isEmpty ? 'Please provide details' : null,
              ),
              const SizedBox(height: 32),

              // Submit Button
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6C63FF),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: _isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text('Submit Ticket', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  InputDecoration _inputDecoration() {
    return InputDecoration(
      filled: true,
      fillColor: Colors.white,
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFF6C63FF), width: 2)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
    );
  }
}
