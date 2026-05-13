import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/support_provider.dart';
import 'package:intl/intl.dart';
import 'create_ticket_screen.dart';

class SupportTicketsScreen extends ConsumerWidget {
  const SupportTicketsScreen({super.key});

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'open':
        return Colors.green;
      case 'waiting':
      case 'in_progress':
        return Colors.orange;
      case 'closed':
        return Colors.grey;
      default:
        return AppColors.primaryNavy;
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final ticketsState = ref.watch(supportTicketsProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Support Tickets', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: AppColors.primaryNavy,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.add_circle, color: Color(0xFF6C63FF)),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const CreateTicketScreen()),
              );
            },
          )
        ],
      ),
      body: ticketsState.when(
        data: (tickets) {
          if (tickets.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.inbox_outlined, size: 64, color: Colors.grey.shade400),
                  const SizedBox(height: 16),
                  const Text('No Support Tickets', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black54)),
                  const SizedBox(height: 8),
                  const Text('Need help? Create a new ticket.', style: TextStyle(color: Colors.black45)),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const CreateTicketScreen()),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF6C63FF),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                    ),
                    child: const Text('Create Ticket', style: TextStyle(color: Colors.white)),
                  )
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () => ref.read(supportTicketsProvider.notifier).fetchTickets(),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: tickets.length,
              itemBuilder: (context, index) {
                final ticket = tickets[index];
                return _buildTicketCard(context, ticket);
              },
            ),
          );
        },
        loading: () => const Center(child: CircularProgressIndicator(color: Color(0xFF6C63FF))),
        error: (err, st) => Center(child: Text('Error: $err')),
      ),
    );
  }

  Widget _buildTicketCard(BuildContext context, dynamic ticket) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4)),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () {
            _showTicketDetails(context, ticket);
          },
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'TICKET #${ticket['id']}',
                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: _getStatusColor(ticket['status']).withOpacity(0.1),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        ticket['status'].toString().toUpperCase(),
                        style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: _getStatusColor(ticket['status'])),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Text(
                  ticket['subject'] ?? 'No Subject',
                  style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppColors.primaryNavy),
                ),
                const SizedBox(height: 4),
                Text(
                  'Created: ${ticket['created_at'] != null ? DateFormat('MMM d, yyyy - HH:mm').format(DateTime.parse(ticket['created_at'])) : 'Unknown'}',
                  style: const TextStyle(fontSize: 12, color: Colors.black54),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _showTicketDetails(BuildContext context, dynamic ticket) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => _TicketDetailsModal(ticket: ticket),
    );
  }
}

class _TicketDetailsModal extends ConsumerStatefulWidget {
  final dynamic ticket;
  const _TicketDetailsModal({required this.ticket});

  @override
  ConsumerState<_TicketDetailsModal> createState() => _TicketDetailsModalState();
}

class _TicketDetailsModalState extends ConsumerState<_TicketDetailsModal> {
  final _replyController = TextEditingController();
  bool _isReplying = false;

  void _submitReply() async {
    if (_replyController.text.trim().isEmpty) return;

    setState(() => _isReplying = true);
    try {
      await ref.read(supportTicketsProvider.notifier).replyToTicket(
        widget.ticket['id'].toString(),
        _replyController.text.trim(),
      );
      if (mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Reply submitted successfully')));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
      }
    } finally {
      if (mounted) setState(() => _isReplying = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final replies = widget.ticket['replies'] ?? [];
    
    return Container(
      height: MediaQuery.of(context).size.height * 0.85,
      decoration: const BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.only(topLeft: Radius.circular(24), topRight: Radius.circular(24)),
      ),
      child: Column(
        children: [
          // Header
          Container(
            padding: const EdgeInsets.all(20),
            decoration: const BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.only(topLeft: Radius.circular(24), topRight: Radius.circular(24)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2))),
                ),
                const SizedBox(height: 16),
                Text(widget.ticket['subject'] ?? 'Ticket Details', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppColors.primaryNavy)),
                const SizedBox(height: 4),
                Text('TICKET #${widget.ticket['id']} • ${widget.ticket['category'].toString().toUpperCase()}', style: const TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.w600)),
              ],
            ),
          ),
          
          // Replies
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: replies.length,
              itemBuilder: (context, index) {
                final reply = replies[index];
                final isCustomer = reply['sender_type'] == 'customer';
                
                return Align(
                  alignment: isCustomer ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.all(16),
                    constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.8),
                    decoration: BoxDecoration(
                      color: isCustomer ? const Color(0xFF6C63FF) : Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 4, offset: const Offset(0, 2))],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          isCustomer ? 'You' : 'Support Agent',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: isCustomer ? Colors.white70 : Colors.black45,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          reply['message'] ?? '',
                          style: TextStyle(
                            color: isCustomer ? Colors.white : AppColors.primaryNavy,
                            fontSize: 14,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          reply['created_at'] != null ? DateFormat('MMM d, HH:mm').format(DateTime.parse(reply['created_at'])) : '',
                          style: TextStyle(
                            color: isCustomer ? Colors.white54 : Colors.grey,
                            fontSize: 10,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
          
          // Reply Box
          if (widget.ticket['status'] != 'closed')
            Container(
              padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom + 16, left: 16, right: 16, top: 16),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5))],
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      decoration: BoxDecoration(color: AppColors.background, borderRadius: BorderRadius.circular(24)),
                      child: TextField(
                        controller: _replyController,
                        decoration: const InputDecoration(hintText: 'Type your reply...', border: InputBorder.none),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  GestureDetector(
                    onTap: _isReplying ? null : _submitReply,
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: const BoxDecoration(color: Color(0xFF6C63FF), shape: BoxShape.circle),
                      child: _isReplying 
                        ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                        : const Icon(Icons.send, color: Colors.white, size: 24),
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
