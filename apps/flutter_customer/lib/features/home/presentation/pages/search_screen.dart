import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import '../../../../core/theme/app_colors.dart';
import '../providers/search_provider.dart';
import '../providers/ride_booking_provider.dart';
import 'choose_rider_screen.dart';

class SearchScreen extends ConsumerStatefulWidget {
  final String? title;
  final bool fromDashboard;
  final bool isPlanningLater;
  final void Function(String address, LatLng location)? onSelect;

  const SearchScreen({
    super.key,
    this.title,
    this.fromDashboard = false,
    this.isPlanningLater = false,
    this.onSelect,
  });

  @override
  ConsumerState<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends ConsumerState<SearchScreen> {
  late bool isLater;
  String selectedRider = 'Me';
  String _pickupText = '';
  String _destinationText = '';

  @override
  void initState() {
    super.initState();
    isLater = widget.isPlanningLater;
  }

  // ── Helpers ─────────────────────────────────────────────────────────────
  Color get _obsidian => AppColors.obsidianDark;
  Color get _glass => Colors.white12;
  Color get _glassStrong => Colors.white.withOpacity(0.15);
  Color get _textPrimary => Colors.white;
  Color get _textMuted => Colors.white54;

  // ── Time Selector Modal ──────────────────────────────────────────────────
  void _showTimeSelector() {
    showModalBottomSheet(
      context: context,
      backgroundColor: _obsidian,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => StatefulBuilder(builder: (ctx, setS) {
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 20),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2))),
                const SizedBox(height: 20),
                Text('When do you need a trip?', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: _textPrimary)),
                const SizedBox(height: 20),
                _timeTile(ctx, setS, icon: Icons.schedule, title: 'Now', sub: 'Request a trip, hop in and go', value: false),
                Divider(color: Colors.white12, height: 1, indent: 64),
                _timeTile(ctx, setS, icon: Icons.calendar_today, title: 'Later', sub: 'Reserve for extra peace of mind', value: true),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: () => Navigator.pop(ctx),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.white,
                    foregroundColor: Colors.black,
                    minimumSize: const Size(double.infinity, 56),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: const Text('Done', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              ],
            ),
          ),
        );
      }),
    );
  }

  Widget _timeTile(BuildContext ctx, StateSetter setS, {required IconData icon, required String title, required String sub, required bool value}) {
    final isSelected = isLater == value;
    return ListTile(
      leading: Icon(icon, color: isSelected ? Colors.white : Colors.white54, size: 28),
      title: Text(title, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: isSelected ? Colors.white : Colors.white70)),
      subtitle: Text(sub, style: TextStyle(color: _textMuted)),
      trailing: Icon(isSelected ? Icons.radio_button_checked : Icons.radio_button_unchecked, color: Colors.white, size: 26),
      onTap: () { setState(() => isLater = value); setS(() {}); },
    );
  }

  // ── Rider Selector Modal ─────────────────────────────────────────────────
  void _showRiderSelector() {
    showModalBottomSheet(
      context: context,
      backgroundColor: _obsidian,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => StatefulBuilder(builder: (ctx, setS) {
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2))),
                const SizedBox(height: 16),
                Text('Switch rider', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: _textPrimary)),
                const SizedBox(height: 12),
                Divider(color: Colors.white12, height: 1),
                _riderTile(ctx, setS, id: 'Becky', name: 'Becky Becky', phone: '+233549595963', initials: 'BB', avatarColor: AppColors.primary),
                Divider(color: Colors.white12, height: 1, indent: 80),
                _riderTile(ctx, setS, id: 'Me', name: 'Me', phone: '', initials: '', isMe: true),
                Divider(color: Colors.white12, height: 1, indent: 80),
                _riderTile(ctx, setS, id: 'Group', name: 'A group', phone: 'Share trip info with others', initials: '', isGroup: true),
                Divider(color: Colors.white12, height: 1, indent: 80),
                ListTile(
                  contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 4),
                  leading: Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle), child: Icon(Icons.person_add, color: _textPrimary, size: 24)),
                  title: Text('Add new contact', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: _textPrimary)),
                  onTap: () {
                    Navigator.pop(ctx);
                    Navigator.push(context, MaterialPageRoute(builder: (_) => const ChooseRiderScreen()));
                  },
                ),
                const SizedBox(height: 16),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: ElevatedButton(
                    onPressed: () => Navigator.pop(ctx),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: Colors.black,
                      minimumSize: const Size(double.infinity, 56),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                    child: const Text('Done', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ),
        );
      }),
    );
  }

  Widget _riderTile(BuildContext ctx, StateSetter setS, {required String id, required String name, required String phone, required String initials, Color? avatarColor, bool isMe = false, bool isGroup = false}) {
    final isSelected = selectedRider == id;
    Widget avatar = isMe
        ? CircleAvatar(backgroundColor: Colors.white12, child: Icon(Icons.person, color: Colors.white54))
        : isGroup
            ? CircleAvatar(backgroundColor: Colors.white12, child: Icon(Icons.group, color: Colors.white54))
            : CircleAvatar(backgroundColor: avatarColor ?? AppColors.primary, child: Text(initials, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)));
    return ListTile(
      contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 4),
      leading: avatar,
      title: Text(name, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: _textPrimary)),
      subtitle: phone.isNotEmpty ? Text(phone, style: TextStyle(color: _textMuted, fontSize: 13)) : null,
      trailing: Icon(isSelected ? Icons.radio_button_checked : Icons.radio_button_unchecked, color: Colors.white, size: 26),
      onTap: () { setState(() => selectedRider = id); setS(() {}); },
    );
  }

  // ── Add Stops Modal ──────────────────────────────────────────────────────
  void _showAddStopsModal() {
    List<Map<String, String>> modalStops = [
      {'id': '1', 'text': _pickupText.isNotEmpty ? _pickupText : 'Taifa S.D.A. Church'},
      {'id': '2', 'text': 'Add stop'},
      {'id': '3', 'text': 'Add stop'},
    ];

    showModalBottomSheet(
      context: context,
      backgroundColor: _obsidian,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => StatefulBuilder(builder: (ctx, setS) {
        bool hasDestination = modalStops.any((s) => s['text'] != 'Add stop' && modalStops.indexOf(s) > 0);
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 20),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2))),
                const SizedBox(height: 16),
                Text('Add stops', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: _textPrimary)),
                const SizedBox(height: 20),
                ReorderableListView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  buildDefaultDragHandles: false,
                  itemCount: modalStops.length,
                  onReorder: (old, nw) {
                    if (old < nw) nw -= 1;
                    setS(() { final item = modalStops.removeAt(old); modalStops.insert(nw, item); });
                  },
                  itemBuilder: (ctx2, index) {
                    final stop = modalStops[index];
                    final isFirst = index == 0;
                    final isLast = index == modalStops.length - 1;
                    return Container(
                      key: ValueKey(stop['id']),
                      padding: const EdgeInsets.symmetric(horizontal: 20),
                      child: Row(children: [
                        Expanded(
                          child: Container(
                            height: 56,
                            decoration: BoxDecoration(
                              color: Colors.white10,
                              borderRadius: BorderRadius.vertical(
                                top: isFirst ? const Radius.circular(16) : Radius.zero,
                                bottom: isLast ? const Radius.circular(16) : Radius.zero,
                              ),
                              border: isLast ? null : Border(bottom: BorderSide(color: Colors.white12)),
                            ),
                            child: Row(children: [
                              SizedBox(width: 48, child: Stack(alignment: Alignment.center, children: [
                                if (!isFirst) Positioned(top: 0, bottom: 28, child: Container(width: 2, color: Colors.white30)),
                                if (!isLast) Positioned(top: 28, bottom: 0, child: Container(width: 2, color: Colors.white30)),
                                if (isFirst) Container(width: 12, height: 12, decoration: BoxDecoration(shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 3)))
                                else if (isLast) Icon(Icons.add, size: 20, color: _textPrimary)
                                else Container(width: 18, height: 18, color: Colors.white, child: Center(child: Text('$index', style: const TextStyle(color: Colors.black, fontSize: 11, fontWeight: FontWeight.bold)))),
                              ])),
                              Expanded(child: GestureDetector(
                                onTap: () async {
                                  final ctrl = TextEditingController(text: stop['text'] == 'Add stop' ? '' : stop['text']);
                                  await showDialog(context: context, builder: (d) => AlertDialog(
                                    backgroundColor: _obsidian,
                                    title: Text('Edit stop', style: TextStyle(color: _textPrimary, fontWeight: FontWeight.bold)),
                                    content: TextField(
                                      controller: ctrl,
                                      autofocus: true,
                                      style: TextStyle(color: _textPrimary),
                                      decoration: InputDecoration(hintText: 'Enter location', hintStyle: TextStyle(color: _textMuted), enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.white30)), focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.white))),
                                    ),
                                    actions: [
                                      TextButton(onPressed: () => Navigator.pop(d), child: Text('Cancel', style: TextStyle(color: _textMuted))),
                                      ElevatedButton(onPressed: () { setS(() => stop['text'] = ctrl.text.isEmpty ? 'Add stop' : ctrl.text); Navigator.pop(d); }, style: ElevatedButton.styleFrom(backgroundColor: Colors.white), child: const Text('Set', style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold))),
                                    ],
                                  ));
                                },
                                child: Text(stop['text']!, style: TextStyle(color: stop['text'] == 'Add stop' ? _textMuted : _textPrimary, fontSize: 16)),
                              )),
                              ReorderableDragStartListener(
                                index: index,
                                child: Padding(padding: const EdgeInsets.symmetric(horizontal: 16), child: Icon(Icons.drag_handle, color: _textMuted)),
                              ),
                            ]),
                          ),
                        ),
                        SizedBox(
                          width: 40,
                          child: (!isFirst && !isLast)
                              ? IconButton(icon: Icon(Icons.close, color: _textPrimary, size: 20), onPressed: () => setS(() => modalStops.removeAt(index)))
                              : null,
                        ),
                      ]),
                    );
                  },
                ),
                const SizedBox(height: 20),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Row(children: [
                    GestureDetector(
                      onTap: () { Navigator.pop(ctx); _showTimeSelector(); },
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                        decoration: BoxDecoration(color: isLater ? Colors.white : Colors.white12, borderRadius: BorderRadius.circular(24)),
                        child: Row(children: [
                          Icon(isLater ? Icons.calendar_today : Icons.schedule, size: 18, color: isLater ? Colors.black : _textPrimary),
                          const SizedBox(width: 8),
                          Text(isLater ? 'Pick-up later' : 'Pick-up now', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: isLater ? Colors.black : _textPrimary)),
                          const SizedBox(width: 4),
                          Icon(Icons.keyboard_arrow_down, size: 20, color: isLater ? Colors.black : _textPrimary),
                        ]),
                      ),
                    ),
                    const SizedBox(width: 12),
                    GestureDetector(
                      onTap: () { Navigator.pop(ctx); _showRiderSelector(); },
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                        decoration: BoxDecoration(color: Colors.white12, borderRadius: BorderRadius.circular(24)),
                        child: Row(children: [
                          Icon(selectedRider == 'Group' ? Icons.group : Icons.person, size: 18, color: _textPrimary),
                          const SizedBox(width: 8),
                          Text(selectedRider == 'Me' ? 'For me' : selectedRider == 'Group' ? 'A group' : selectedRider, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: _textPrimary)),
                          const SizedBox(width: 4),
                          Icon(Icons.keyboard_arrow_down, size: 20, color: _textPrimary),
                        ]),
                      ),
                    ),
                  ]),
                ),
                const SizedBox(height: 20),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: ElevatedButton(
                    onPressed: () => Navigator.pop(ctx),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: Colors.black,
                      minimumSize: const Size(double.infinity, 56),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                    child: const Text('Done', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ),
        );
      }),
    );
  }

  // ── Build ────────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final suggestions = ref.watch(searchProvider);

    return Scaffold(
      backgroundColor: _obsidian,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: _textPrimary, size: 28),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(widget.title ?? 'Plan your trip', style: TextStyle(color: _textPrimary, fontWeight: FontWeight.bold, fontSize: 20)),
        centerTitle: true,
      ),
      body: Column(
        children: [
          // ── Pills ──
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
            child: Row(children: [
              GestureDetector(
                onTap: _showTimeSelector,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                  decoration: BoxDecoration(color: isLater ? Colors.white : Colors.white12, borderRadius: BorderRadius.circular(24)),
                  child: Row(children: [
                    Icon(isLater ? Icons.calendar_today : Icons.schedule, size: 18, color: isLater ? Colors.black : _textPrimary),
                    const SizedBox(width: 8),
                    Text(isLater ? 'Pick-up later' : 'Pick-up now', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: isLater ? Colors.black : _textPrimary)),
                    const SizedBox(width: 4),
                    Icon(Icons.keyboard_arrow_down, size: 20, color: isLater ? Colors.black : _textPrimary),
                  ]),
                ),
              ),
              const SizedBox(width: 12),
              GestureDetector(
                onTap: _showRiderSelector,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                  decoration: BoxDecoration(color: Colors.white12, borderRadius: BorderRadius.circular(24)),
                  child: Row(children: [
                    Icon(selectedRider == 'Group' ? Icons.group : Icons.person, size: 18, color: _textPrimary),
                    const SizedBox(width: 8),
                    Text(selectedRider == 'Me' ? 'For me' : selectedRider == 'Group' ? 'A group' : selectedRider, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: _textPrimary)),
                    const SizedBox(width: 4),
                    Icon(Icons.keyboard_arrow_down, size: 20, color: _textPrimary),
                  ]),
                ),
              ),
            ]),
          ),

          // ── Input fields ──
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
            child: Row(children: [
              Expanded(
                child: Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: Colors.white24, width: 1.5),
                    color: Colors.white10,
                  ),
                  child: Row(children: [
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                        Container(width: 10, height: 10, decoration: BoxDecoration(shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 3))),
                        Container(width: 2, height: 36, color: Colors.white30),
                        Container(width: 10, height: 10, color: Colors.white),
                      ]),
                    ),
                    Expanded(
                      child: Column(children: [
                        TextField(
                          style: TextStyle(color: _textPrimary, fontSize: 16),
                          onChanged: (v) => setState(() => _pickupText = v),
                          decoration: InputDecoration(
                            hintText: 'Enter pick-up location',
                            hintStyle: TextStyle(color: Colors.white70, fontSize: 16),
                            filled: true,
                            fillColor: Colors.transparent,
                            border: InputBorder.none,
                            enabledBorder: InputBorder.none,
                            focusedBorder: InputBorder.none,
                            errorBorder: InputBorder.none,
                            focusedErrorBorder: InputBorder.none,
                            isDense: true,
                            contentPadding: const EdgeInsets.symmetric(vertical: 16, horizontal: 4),
                          ),
                        ),
                        Container(height: 1, color: Colors.white12),
                        TextField(
                          autofocus: true,
                          style: TextStyle(color: _textPrimary, fontWeight: FontWeight.bold, fontSize: 16),
                          onChanged: (val) {
                            setState(() => _destinationText = val);
                            ref.read(searchProvider.notifier).onQueryChanged(val);
                          },
                          decoration: InputDecoration(
                            hintText: 'Where to?',
                            hintStyle: TextStyle(fontWeight: FontWeight.bold, color: Colors.white70, fontSize: 16),
                            filled: true,
                            fillColor: Colors.transparent,
                            border: InputBorder.none,
                            enabledBorder: InputBorder.none,
                            focusedBorder: InputBorder.none,
                            errorBorder: InputBorder.none,
                            focusedErrorBorder: InputBorder.none,
                            isDense: true,
                            contentPadding: const EdgeInsets.symmetric(vertical: 16, horizontal: 4),
                          ),
                        ),
                      ]),
                    ),
                  ]),
                ),
              ),
              const SizedBox(width: 12),
              GestureDetector(
                onTap: _showAddStopsModal,
                child: Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle),
                  child: Icon(Icons.add, color: _textPrimary, size: 24),
                ),
              ),
            ]),
          ),

          const SizedBox(height: 8),
          Divider(color: Colors.white12, height: 1),

          // ── List ──
          Expanded(
            child: suggestions.isEmpty ? _buildEmptyState() : ListView.separated(
              itemCount: suggestions.length,
              separatorBuilder: (_, _) => Divider(color: Colors.white12, height: 1, indent: 64),
              itemBuilder: (context, index) {
                final place = suggestions[index];
                return ListTile(
                  contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
                  leading: Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle),
                    child: Icon(Icons.location_on, color: _textPrimary, size: 20),
                  ),
                  title: Text(place.mainText, style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: _textPrimary)),
                  subtitle: Text(place.secondaryText, overflow: TextOverflow.ellipsis, style: TextStyle(color: _textMuted)),
                  onTap: () async {
                    final latLng = await ref.read(locationRepositoryProvider).getPlaceDetails(place.placeId);
                    if (latLng != null) {
                      if (widget.onSelect != null) {
                        widget.onSelect!(place.mainText, latLng);
                      } else {
                        ref.read(rideBookingProvider.notifier).setDropoff(latLng);
                      }
                      if (context.mounted) Navigator.pop(context);
                    }
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return ListView(
      children: [
        ListTile(
          contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
          leading: Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle), child: Icon(Icons.star_border, color: _textPrimary, size: 20)),
          title: Text('Saved places', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: _textPrimary)),
          onTap: () {},
        ),
        Divider(color: Colors.white12, height: 1, indent: 64),
        
        // Work
        ListTile(
          contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
          leading: Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle), child: Icon(Icons.work, color: _textPrimary, size: 20)),
          title: Text('Work', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: _textPrimary)),
          subtitle: Text('1455 Market St', style: TextStyle(color: _textMuted)),
          onTap: () {
            ref.read(rideBookingProvider.notifier).setDropoff(
              LatLng(5.6037, -0.1870), 
              address: '1455 Market St'
            );
            Navigator.pop(context);
          },
        ),
        Divider(color: Colors.white12, height: 1, indent: 64),

        // Home
        ListTile(
          contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
          leading: Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle), child: Icon(Icons.home, color: _textPrimary, size: 20)),
          title: Text('Home', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: _textPrimary)),
          subtitle: Text('903 Sunrose Terr', style: TextStyle(color: _textMuted)),
          onTap: () {
            ref.read(rideBookingProvider.notifier).setDropoff(
              LatLng(5.6148, -0.2058), 
              address: '903 Sunrose Terr'
            );
            Navigator.pop(context);
          },
        ),
        Divider(color: Colors.white12, height: 1, indent: 64),

        ListTile(
          contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
          leading: Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle), child: Icon(Icons.schedule, color: _textPrimary, size: 20)),
          title: Text('Taifa S.D.A. Church', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: _textPrimary)),
          subtitle: Text('MP4X+GG4, Taifa', style: TextStyle(color: _textMuted)),
          onTap: () {
            ref.read(rideBookingProvider.notifier).setDropoff(
              LatLng(5.6667, -0.2333), 
              address: 'Taifa S.D.A. Church'
            );
            Navigator.pop(context);
          },
        ),
        Divider(color: Colors.white12, height: 1, indent: 64),
        ListTile(
          contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
          leading: Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle), child: Icon(Icons.language, color: _textPrimary, size: 20)),
          title: Text('Search in a different city', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: _textPrimary)),
          onTap: () {},
        ),
        Divider(color: Colors.white12, height: 1, indent: 64),
        ListTile(
          contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
          leading: Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: Colors.white12, shape: BoxShape.circle), child: Icon(Icons.my_location, color: _textPrimary, size: 20)),
          title: Text('Set location on map', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: _textPrimary)),
          onTap: () { Navigator.pop(context); },
        ),
      ],
    );
  }
}
