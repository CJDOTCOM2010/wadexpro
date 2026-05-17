import 'package:flutter/material.dart';
import 'package:video_player/video_player.dart';

class PlatformMediaWidget extends StatefulWidget {
  final String url;
  final String mediaType; // 'image' or 'video'
  final BoxFit fit;
  final double? width;
  final double? height;
  final Color? placeholderColor;
  final Widget? errorWidget;

  const PlatformMediaWidget({
    super.key,
    required this.url,
    required this.mediaType,
    this.fit = BoxFit.cover,
    this.width,
    this.height,
    this.placeholderColor,
    this.errorWidget,
  });

  @override
  State<PlatformMediaWidget> createState() => _PlatformMediaWidgetState();
}

class _PlatformMediaWidgetState extends State<PlatformMediaWidget> {
  VideoPlayerController? _videoController;
  bool _isInitialized = false;

  @override
  void initState() {
    super.initState();
    if (widget.mediaType == 'video') {
      _initializeVideo();
    }
  }

  @override
  void didUpdateWidget(PlatformMediaWidget oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.url != oldWidget.url || widget.mediaType != oldWidget.mediaType) {
      if (_videoController != null) {
        _videoController!.dispose();
        _videoController = null;
        _isInitialized = false;
      }
      if (widget.mediaType == 'video') {
        _initializeVideo();
      }
    }
  }

  Future<void> _initializeVideo() async {
    _videoController = VideoPlayerController.networkUrl(Uri.parse(widget.url));
    try {
      await _videoController!.initialize();
      await _videoController!.setLooping(true);
      await _videoController!.setVolume(0); // Muted by default for splash/onboarding
      await _videoController!.play();
      if (mounted) {
        setState(() {
          _isInitialized = true;
        });
      }
    } catch (e) {
      debugPrint('WADEXPRO: Video initialization failed: $e');
    }
  }

  @override
  void dispose() {
    _videoController?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (widget.mediaType == 'video') {
      if (_isInitialized && _videoController != null) {
        final videoSize = _videoController!.value.size;
        if (videoSize.width <= 0 || videoSize.height <= 0) {
          return _buildPlaceholder();
        }
        return LayoutBuilder(
          builder: (context, constraints) {
            final scaleX = constraints.maxWidth / videoSize.width;
            final scaleY = constraints.maxHeight / videoSize.height;
            final scale = widget.fit == BoxFit.cover
                ? scaleX > scaleY ? scaleX : scaleY
                : scaleX < scaleY ? scaleX : scaleY;
            return ClipRect(
              child: OverflowBox(
                alignment: Alignment.center,
                maxWidth: videoSize.width * scale,
                maxHeight: videoSize.height * scale,
                child: SizedBox(
                  width: videoSize.width * scale,
                  height: videoSize.height * scale,
                  child: VideoPlayer(_videoController!),
                ),
              ),
            );
          },
        );
      }
      return _buildPlaceholder();
    }

    // Default to Image (supports animated GIFs natively)
    return Image.network(
      widget.url,
      fit: widget.fit,
      width: widget.width,
      height: widget.height,
      loadingBuilder: (context, child, loadingProgress) {
        if (loadingProgress == null) return child;
        return _buildPlaceholder(progress: loadingProgress);
      },
      errorBuilder: (context, error, stackTrace) {
        debugPrint('WADEXPRO: Image load failed: $error');
        return widget.errorWidget ?? _buildPlaceholder(isError: true);
      },
    );
  }

  Widget _buildPlaceholder({ImageChunkEvent? progress, bool isError = false}) {
    return Container(
      width: widget.width ?? double.infinity,
      height: widget.height ?? double.infinity,
      color: widget.placeholderColor ?? Colors.black12,
      child: Center(
        child: isError
            ? const Icon(Icons.broken_image_outlined, color: Colors.white24, size: 40)
            : progress != null
                ? CircularProgressIndicator(
                    value: progress.expectedTotalBytes != null
                        ? progress.cumulativeBytesLoaded / progress.expectedTotalBytes!
                        : null,
                    strokeWidth: 2,
                    color: Colors.white24,
                  )
                : const CircularProgressIndicator(strokeWidth: 2, color: Colors.white24),
      ),
    );
  }
}
