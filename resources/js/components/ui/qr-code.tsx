import { useEffect, useRef } from 'react';

interface QRCodeProps {
  value: string;
  size?: number;
  className?: string;
}

export function QRCode({ value, size = 128, className = '' }: QRCodeProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    if (!canvasRef.current) return;

    // Simple QR code placeholder - in production, you'd use a proper QR library
    const canvas = canvasRef.current;
    const ctx = canvas.getContext('2d');
    
    if (!ctx) return;

    // Set canvas size
    canvas.width = size;
    canvas.height = size;

    // Clear canvas
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, size, size);

    // Draw a simple pattern as placeholder
    ctx.fillStyle = '#000000';
    const cellSize = size / 21; // Standard QR code is 21x21 modules

    // Draw corner markers
    const drawCornerMarker = (x: number, y: number) => {
      // Outer square
      ctx.fillRect(x * cellSize, y * cellSize, 7 * cellSize, 7 * cellSize);
      ctx.fillStyle = '#ffffff';
      ctx.fillRect((x + 1) * cellSize, (y + 1) * cellSize, 5 * cellSize, 5 * cellSize);
      ctx.fillStyle = '#000000';
      ctx.fillRect((x + 2) * cellSize, (y + 2) * cellSize, 3 * cellSize, 3 * cellSize);
    };

    drawCornerMarker(0, 0);   // Top-left
    drawCornerMarker(14, 0);  // Top-right
    drawCornerMarker(0, 14);  // Bottom-left

    // Draw some data pattern (simplified)
    for (let i = 0; i < 21; i++) {
      for (let j = 0; j < 21; j++) {
        // Skip corner markers
        if ((i < 9 && j < 9) || (i < 9 && j > 11) || (i > 11 && j < 9)) continue;
        
        // Simple pattern based on position
        if ((i + j) % 3 === 0) {
          ctx.fillRect(i * cellSize, j * cellSize, cellSize, cellSize);
        }
      }
    }

    // Add border
    ctx.strokeStyle = '#e5e7eb';
    ctx.lineWidth = 2;
    ctx.strokeRect(1, 1, size - 2, size - 2);

  }, [value, size]);

  return (
    <div className={`inline-block ${className}`}>
      <canvas 
        ref={canvasRef}
        className="border border-gray-200 rounded-lg"
        style={{ width: size, height: size }}
      />
      <div className="mt-2 text-center">
        <p className="text-xs text-muted-foreground break-all">
          {value}
        </p>
      </div>
    </div>
  );
}
