import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import WebcamCapture from '../components/WebcamCapture.jsx';
import { useAuth } from '../hooks/useAuth';

export default function NewAccessPage() {
  const { api } = useAuth();
  const [photo, setPhoto] = useState(null);
  const [departments, setDepartments] = useState([]);
  const [badgeUrl, setBadgeUrl] = useState(null);
  const { register, handleSubmit, reset, setValue, watch } = useForm({
    defaultValues: {
      full_name: '',
      document_number: '',
      email: '',
      phone: '',
      consent: false,
      department_id: '',
      purpose: ''
    }
  });
  const consent = watch('consent');

  useEffect(() => {
    async function fetchDepartments() {
      try {
        const { data } = await api.get('/departments');
        setDepartments(data);
      } catch (error) {
        console.warn('Falha ao carregar setores. Cadastre via backend.', error);
      }
    }
    fetchDepartments();
  }, [api]);

  const onSubmit = async (values) => {
    if (!photo) {
      alert('Capture uma foto do visitante.');
      return;
    }

    try {
      const visitorPayload = {
        full_name: values.full_name,
        document_number: values.document_number,
        email: values.email,
        phone: values.phone,
        consent: values.consent,
        photo_base64: photo
      };
      const { data: visitor } = await api.post('/visitors', visitorPayload);
      const { data: access } = await api.post('/accesses', {
        visitor_id: visitor.id,
        department_id: values.department_id,
        purpose: values.purpose
      });
      setBadgeUrl(access.badge_url);
      alert('Acesso registrado com sucesso!');
      reset();
      setPhoto(null);
    } catch (error) {
      console.error(error);
      alert('Erro ao registrar acesso. Verifique os dados.');
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-semibold">Novo Acesso</h2>
        <p className="text-slate-400">Cadastre o visitante e gere o crachá automaticamente.</p>
      </div>
      <form onSubmit={handleSubmit(onSubmit)} className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <section className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium">Nome Completo</label>
              <input {...register('full_name', { required: true })} />
            </div>
            <div>
              <label className="block text-sm font-medium">Documento</label>
              <input {...register('document_number', { required: true })} />
            </div>
            <div>
              <label className="block text-sm font-medium">E-mail</label>
              <input type="email" {...register('email')} />
            </div>
            <div>
              <label className="block text-sm font-medium">Telefone</label>
              <input {...register('phone')} />
            </div>
            <div className="md:col-span-2">
              <label className="block text-sm font-medium">Setor a Visitar</label>
              <select {...register('department_id', { required: true })}>
                <option value="">Selecione...</option>
                {departments.map((dept) => (
                  <option key={dept.id} value={dept.id}>
                    {dept.name}
                  </option>
                ))}
              </select>
            </div>
            <div className="md:col-span-2">
              <label className="block text-sm font-medium">Motivo da Visita</label>
              <textarea rows="3" {...register('purpose', { required: true })} />
            </div>
            <label className="flex items-center gap-2 text-sm">
              <input
                type="checkbox"
                {...register('consent')}
                checked={consent}
                onChange={(e) => setValue('consent', e.target.checked)}
              />
              Confirmo o consentimento LGPD
            </label>
          </div>
          <button type="submit" className="w-full">Registrar Acesso</button>
          {badgeUrl && (
            <a href={badgeUrl} target="_blank" rel="noreferrer" className="block text-center underline text-sky-400">
              Baixar crachá em PDF
            </a>
          )}
        </section>
        <section>
          <h3 className="text-lg font-medium mb-4">Captura de Foto</h3>
          <WebcamCapture value={photo} onChange={setPhoto} />
        </section>
      </form>
    </div>
  );
}
