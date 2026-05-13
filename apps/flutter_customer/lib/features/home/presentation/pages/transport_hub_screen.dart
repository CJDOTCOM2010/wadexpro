import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/thumb_reach_bottom_sheet.dart';
import '../../../../core/widgets/wadex_snackbar.dart';
import '../../../../core/network/api_client.dart';
import 'package:dio/dio.dart';

class TransportHubScreen extends StatefulWidget {
  final String serviceName;
  final IconData icon;

  const TransportHubScreen({
    super.key,
    required this.serviceName,
    required this.icon,
  });

  @override
  State<TransportHubScreen> createState() => _TransportHubScreenState();
}

class _TransportHubScreenState extends State<TransportHubScreen> {
  bool _isLoading = true;
  Map<String, dynamic>? _transportData;

  @override
  void initState() {
    super.initState();
    _fetchHubs();
  }

  Future<void> _fetchHubs() async {
    try {
      final response = await ApiClient().instance.get('/v1/logistics/hubs/${widget.serviceName}');
      setState(() {
        _transportData = response.data['data'];
        _isLoading = false;
      });
    } catch (e) {
      // Fallback
      setState(() {
        _transportData = _getFallbackData(widget.serviceName);
        _isLoading = false;
      });
    }
  }

  // ── Transport theme color ─────────────────────────────────────────────
  Color _accentColor() {
    switch (widget.serviceName) {
      case '2-Wheels': return const Color(0xFF00D4AA);
      case 'Transit':  return const Color(0xFF74B9FF);
      case 'Charter':  return const Color(0xFFE17055);
      case 'Travel':   return const Color(0xFF6C63FF);
      default:         return const Color(0xFFF9CA24);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: AppColors.obsidianDark,
        body: const Center(child: CircularProgressIndicator(color: Colors.white)),
      );
    }

    final transportData = _transportData!;
    final accent = _accentColor();

    return Scaffold(
      backgroundColor: AppColors.obsidianDark,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          // ── Premium Hero Header ──────────────────────────────────────
          SliverAppBar(
            expandedHeight: 300.0,
            floating: false,
            pinned: true,
            backgroundColor: AppColors.obsidianDark,
            elevation: 0,
            leading: Container(
              margin: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.black.withOpacity(0.4),
                shape: BoxShape.circle,
              ),
              child: IconButton(
                icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 18),
                onPressed: () => Navigator.pop(context),
              ),
            ),
            flexibleSpace: FlexibleSpaceBar(
              titlePadding: const EdgeInsets.only(left: 20, bottom: 16),
              title: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(widget.icon, color: accent, size: 18),
                  const SizedBox(width: 8),
                  Text(
                    widget.serviceName,
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w900,
                      fontSize: 20,
                      letterSpacing: -0.3,
                    ),
                  ),
                ],
              ),
              background: Stack(
                fit: StackFit.expand,
                children: [
                  Image.network(
                    transportData['headerImage'] ?? '',
                    fit: BoxFit.cover,
                    errorBuilder: (ctx, err, st) => Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [accent.withOpacity(0.3), AppColors.obsidianDark],
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                        ),
                      ),
                      child: Center(child: Icon(widget.icon, color: Colors.white12, size: 80)),
                    ),
                  ),
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        stops: const [0.0, 0.4, 0.75, 1.0],
                        colors: [
                          Colors.black.withOpacity(0.3),
                          Colors.transparent,
                          AppColors.obsidianDark.withOpacity(0.7),
                          AppColors.obsidianDark,
                        ],
                      ),
                    ),
                  ),
                  // Category badge
                  Positioned(
                    bottom: 60,
                    left: 20,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                      decoration: BoxDecoration(
                        color: accent.withOpacity(0.2),
                        borderRadius: BorderRadius.circular(30),
                        border: Border.all(color: accent.withOpacity(0.4)),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.verified_rounded, color: accent, size: 14),
                          const SizedBox(width: 6),
                          Text(
                            'ELITE SERVICE',
                            style: TextStyle(color: accent, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.5),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          // ── Content ──────────────────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 24, 20, 0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    transportData['tagline'] ?? '',
                    style: TextStyle(color: Colors.white.withOpacity(0.7), fontSize: 16, height: 1.5),
                  ).animate().fadeIn(duration: 400.ms),
                  const SizedBox(height: 24),

                  // Safety card (dark themed)
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: accent.withOpacity(0.08),
                      borderRadius: BorderRadius.circular(22),
                      border: Border.all(color: accent.withOpacity(0.15)),
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: accent.withOpacity(0.15),
                            shape: BoxShape.circle,
                          ),
                          child: Icon(Icons.verified_user_rounded, color: accent, size: 26),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('WADEX Safe-Travel', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: Colors.white)),
                              const SizedBox(height: 4),
                              Text('All vehicles and hubs are verified and tracked 24/7.', style: TextStyle(color: Colors.white.withOpacity(0.5), fontSize: 13)),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ).animate().fadeIn(delay: 100.ms).slideY(begin: 0.1),

                  const SizedBox(height: 32),

                  Row(
                    children: [
                      Container(width: 4, height: 18, decoration: BoxDecoration(color: accent, borderRadius: BorderRadius.circular(2))),
                      const SizedBox(width: 10),
                      Text('${widget.serviceName.toUpperCase()} HUBS', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.white38, letterSpacing: 2)),
                      const Spacer(),
                      Text('${(transportData['hubs'] as List).length} locations', style: TextStyle(color: accent.withOpacity(0.7), fontSize: 12, fontWeight: FontWeight.w600)),
                    ],
                  ),
                  const SizedBox(height: 16),
                ],
              ),
            ),
          ),

          // ── Hub List ─────────────────────────────────────────────────
          SliverPadding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            sliver: SliverList(
              delegate: SliverChildBuilderDelegate(
                (context, index) {
                  final hub = transportData['hubs'][index];
                  return _buildHubCard(context, hub, accent)
                      .animate()
                      .fadeIn(duration: 500.ms, delay: (index * 100).ms)
                      .slideY(begin: 0.1, duration: 500.ms, curve: Curves.easeOutCubic, delay: (index * 100).ms);
                },
                childCount: (transportData['hubs'] as List).length,
              ),
            ),
          ),
          const SliverToBoxAdapter(child: SizedBox(height: 80)),
        ],
      ),
    );
  }

  Map<String, dynamic> _getFallbackData(String name) {
    return {
      'headerImage': 'https://images.unsplash.com/photo-1436491865332-7a61a109c0f2?q=80&w=2070&auto=format&fit=crop',
      'tagline': 'Advanced transportation and logistics solutions.',
      'hubs': [
        {'name': 'WADEX Logistics Hub', 'address': 'Tema Harbor Area, Tema', 'phone': '+233 24 555 4001', 'info': 'Full Service Logistics'},
      ]
    };
  }

  // ── Hub card (dark themed, visible text) ──────────────────────────────
  Widget _buildHubCard(BuildContext context, Map<String, dynamic> hub, Color accent) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: GestureDetector(
        onTap: () => _showHubDetails(context, hub, accent),
        child: Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.05),
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: Colors.white.withOpacity(0.08)),
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  gradient: LinearGradient(colors: [accent, accent.withOpacity(0.7)]),
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [BoxShadow(color: accent.withOpacity(0.3), blurRadius: 12, offset: const Offset(0, 4))],
                ),
                child: Icon(icon, color: Colors.white, size: 26),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(hub['name'], style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: Colors.white)),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(Icons.location_on_rounded, color: Colors.white.withOpacity(0.35), size: 13),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            hub['address'],
                            style: TextStyle(color: Colors.white.withOpacity(0.5), fontSize: 13),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: accent.withOpacity(0.12),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(hub['info'], style: TextStyle(color: accent, fontWeight: FontWeight.w700, fontSize: 11)),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.06),
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.arrow_forward_ios_rounded, color: Colors.white.withOpacity(0.3), size: 14),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showHubDetails(BuildContext context, Map<String, dynamic> hub, Color accent) {
    ThumbReachBottomSheet.show(
      context,
      title: hub['name'],
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildDetailRow(Icons.location_on_rounded, hub['address'], accent),
          _buildDetailRow(Icons.phone_rounded, hub['phone'], accent),
          _buildDetailRow(Icons.info_outline_rounded, hub['info'], accent),
          const SizedBox(height: 24),
          GestureDetector(
            onTap: () {
              final uri = Uri(scheme: 'tel', path: hub['phone']);
              launchUrl(uri);
            },
            child: Container(
              width: double.infinity,
              height: 56,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.06),
                borderRadius: BorderRadius.circular(18),
                border: Border.all(color: Colors.white.withOpacity(0.1)),
              ),
              child: const Center(
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.phone_rounded, color: Colors.white70, size: 18),
                    SizedBox(width: 10),
                    Text('Call Hub', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 15)),
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(height: 12),
          GestureDetector(
            onTap: () async {
              showDialog(
                context: context,
                barrierDismissible: false,
                builder: (_) => const Center(child: CircularProgressIndicator(color: Colors.white)),
              );

              try {
                await ApiClient().instance.post('/v1/logistics/book', data: {
                  'service_type': widget.serviceName,
                  'item_id': hub['id'] ?? 0,
                });
                if (context.mounted) {
                  Navigator.pop(context); // loading
                  Navigator.pop(context); // bottom sheet
                  WadexSnackBar.showSuccess(context, 'Successfully booked at ${hub['name']}');
                }
              } catch (e) {
                if (context.mounted) {
                  Navigator.pop(context);
                  WadexSnackBar.showError(context, 'Failed to book at this hub');
                }
              }
            },
            child: Container(
              width: double.infinity,
              height: 56,
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: [accent, accent.withOpacity(0.7)]),
                borderRadius: BorderRadius.circular(18),
                boxShadow: [BoxShadow(color: accent.withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 5))],
              ),
              child: const Center(
                child: Text('Book at this Hub', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 16)),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDetailRow(IconData detailIcon, String text, Color accent) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: accent.withOpacity(0.12),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(detailIcon, color: accent, size: 22),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Text(text, style: const TextStyle(fontSize: 15, color: Colors.white, fontWeight: FontWeight.w500)),
          ),
        ],
      ),
    );
  }
}
