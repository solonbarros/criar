import { useEffect, useRef } from 'react';

/**
 * Capture photos via WebRTC and return base64 payload to parent component.
 */
export default function WebcamCapture({ value, onChange }) {
  const videoRef = useRef(null);
  const canvasRef = useRef(null);

  useEffect(() => {
    let stream;
    async function init() {
      try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
        if (videoRef.current) {
          videoRef.current.srcObject = stream;
        }
      } catch (error) {
        alert('Não foi possível acessar a câmera.');
        console.error(error);
      }
    }
    init();

    return () => {
      stream?.getTracks().forEach((track) => track.stop());
    };
  }, []);

  const capturePhoto = () => {
    const video = videoRef.current;
    const canvas = canvasRef.current;
    if (!video || !canvas) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);
    const dataUrl = canvas.toDataURL('image/jpeg');
    onChange(dataUrl);
  };

  return (
    <div className="space-y-4">
      <video ref={videoRef} autoPlay playsInline className="w-full rounded border border-slate-700" />
      <canvas ref={canvasRef} className="hidden" />
      <button type="button" onClick={capturePhoto} className="w-full">
        Capturar Foto
      </button>
      {value && <img src={value} alt="Prévia da foto" className="w-full rounded border border-slate-700" />}
    </div>
  );
}
