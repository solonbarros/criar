import { useCallback, useEffect, useRef, useState } from 'react';
import { BrowserMultiFormatReader } from 'zxing-js/library';
import { useAuth } from '../hooks/useAuth';

export default function QrScannerPage() {
  const videoRef = useRef(null);
  const codeReaderRef = useRef(null);
  const { api } = useAuth();
  const [message, setMessage] = useState('Aponte o QR Code do crachá para a câmera.');

  useEffect(() => {
    codeReaderRef.current = new BrowserMultiFormatReader();
    const reader = codeReaderRef.current;

    async function startScanner() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        if (videoRef.current) {
          videoRef.current.srcObject = stream;
          await videoRef.current.play();
        }
        reader.decodeFromVideoElementContinuously(videoRef.current, async (result, error) => {
          if (result) {
            await handleToken(result.getText());
          }
          if (error?.name === 'NotFoundException') {
            return;
          }
          if (error) {
            console.error(error);
          }
        });
      } catch (err) {
        console.error(err);
        alert('Não foi possível iniciar o leitor de QR Code.');
      }
    }

    startScanner();

    return () => {
      reader?.reset();
      const tracks = videoRef.current?.srcObject?.getTracks() ?? [];
      tracks.forEach((track) => track.stop());
    };
  }, []);

  const handleToken = useCallback(
    async (token) => {
      if (!token) return;
      try {
        const { data } = await api.post('/scan', { token });
        setMessage(`Saída registrada para ${data.access.visitor.full_name}`);
      } catch (error) {
        setMessage('QR Code inválido ou já utilizado.');
        console.error(error);
      }
    },
    [api]
  );

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-semibold">Leitor de QR Code</h2>
        <p className="text-slate-400">Faça a leitura para registrar a saída em segundos.</p>
      </div>
      <div className="grid lg:grid-cols-2 gap-8">
        <div>
          <video ref={videoRef} className="w-full rounded border border-slate-700" />
        </div>
        <div className="bg-slate-900 p-6 rounded">
          <h3 className="text-lg font-medium mb-2">Status</h3>
          <p>{message}</p>
        </div>
      </div>
    </div>
  );
}
