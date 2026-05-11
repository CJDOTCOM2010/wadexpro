import 'dart:async';
import 'package:flutter/material.dart';

class StorySlider extends StatefulWidget {
  final List<StoryData> stories;
  const StorySlider({super.key, required this.stories});

  @override
  State<StorySlider> createState() => _StorySliderState();
}

class _StorySliderState extends State<StorySlider> {
  int _currentIndex = 0;
  double _progress = 0.0;
  Timer? _timer;
  bool _isPaused = false;

  @override
  void initState() {
    super.initState();
    _startTimer();
  }

  void _startTimer() {
    _timer?.cancel();
    _timer = Timer.periodic(const Duration(milliseconds: 50), (timer) {
      if (!_isPaused) {
        setState(() {
          if (_progress < 1.0) {
            _progress += 0.01;
          } else {
            _nextStory();
          }
        });
      }
    });
  }

  void _nextStory() {
    if (_currentIndex < widget.stories.length - 1) {
      setState(() {
        _currentIndex++;
        _progress = 0.0;
      });
    } else {
      setState(() {
        _currentIndex = 0;
        _progress = 0.0;
      });
    }
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final story = widget.stories[_currentIndex];

    return GestureDetector(
      onLongPressStart: (_) => setState(() => _isPaused = true),
      onLongPressEnd: (_) => setState(() => _isPaused = false),
      onTapUp: (details) {
        final screenWidth = MediaQuery.of(context).size.width;
        if (details.globalPosition.dx < screenWidth / 3) {
          if (_currentIndex > 0) {
            setState(() {
              _currentIndex--;
              _progress = 0.0;
            });
          }
        } else {
          _nextStory();
        }
      },
      child: ClipRRect(
        borderRadius: BorderRadius.circular(24),
        child: SizedBox(
          height: 400,
          width: double.infinity,
          child: Stack(
            fit: StackFit.expand,
            children: [
              Image.network(
                story.imagePath,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) {
                  return Container(
                    color: Colors.white10,
                    child: const Icon(Icons.image_not_supported, color: Colors.white24, size: 64),
                  );
                },
                loadingBuilder: (context, child, loadingProgress) {
                  if (loadingProgress == null) return child;
                  return Container(
                    color: Colors.white10,
                    child: const Center(child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white24)),
                  );
                },
              ),
              Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.black.withOpacity(0.4),
                      Colors.transparent,
                      Colors.black.withOpacity(0.7),
                    ],
                  ),
                ),
              ),
            // Progress Bars
            Position(
              top: 16,
              left: 16,
              right: 16,
              child: Row(
                children: List.generate(widget.stories.length, (index) {
                  return Expanded(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 2),
                      child: LinearProgressIndicator(
                        value: index == _currentIndex ? _progress : (index < _currentIndex ? 1.0 : 0.0),
                        backgroundColor: Colors.white24,
                        valueColor: const AlwaysStoppedAnimation<Color>(Colors.white),
                        minHeight: 3,
                      ),
                    ),
                  );
                }),
              ),
            ),
            // Play/Pause Overlay Icon (Briefly shown when pausing)
            if (_isPaused)
              const Center(
                child: Icon(Icons.pause, color: Colors.white54, size: 64),
              ),
            // Content
            Positioned(
              bottom: 32,
              left: 24,
              right: 24,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    story.title,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 32,
                      fontWeight: FontWeight.w900,
                      height: 1.1,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    story.subtitle,
                    style: TextStyle(
                      color: Colors.white.withOpacity(0.9),
                      fontSize: 16,
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: story.onTap,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: Colors.black,
                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(story.ctaText, style: const TextStyle(fontWeight: FontWeight.bold)),
                        const SizedBox(width: 8),
                        const Icon(Icons.arrow_forward, size: 18),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    ),);
  }
}

class Position extends StatelessWidget {
  final double? top, bottom, left, right;
  final Widget child;
  const Position({super.key, this.top, this.bottom, this.left, this.right, required this.child});
  @override
  Widget build(BuildContext context) => Positioned(top: top, bottom: bottom, left: left, right: right, child: child);
}

class StoryData {
  final String title;
  final String subtitle;
  final String imagePath;
  final String ctaText;
  final VoidCallback onTap;

  StoryData({
    required this.title,
    required this.subtitle,
    required this.imagePath,
    required this.ctaText,
    required this.onTap,
  });
}
