import { useForm } from 'react-hook-form';
import { useAuth } from '../hooks/useAuth';

export default function LoginPage() {
  const { register, handleSubmit, formState } = useForm({
    defaultValues: { email: '', password: '' }
  });
  const { login } = useAuth();

  const onSubmit = async (values) => {
    try {
      await login(values);
    } catch (error) {
      alert('Não foi possível autenticar. Verifique as credenciais.');
      console.error(error);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-950">
      <form onSubmit={handleSubmit(onSubmit)} className="bg-slate-900 p-10 rounded-xl shadow-xl w-full max-w-md space-y-6">
        <div>
          <h1 className="text-3xl font-bold text-center">Controle de Acesso</h1>
          <p className="text-center text-slate-400">Entre com suas credenciais</p>
        </div>
        <div className="space-y-2">
          <label htmlFor="email" className="block text-sm font-medium">E-mail</label>
          <input id="email" type="email" {...register('email', { required: true })} />
        </div>
        <div className="space-y-2">
          <label htmlFor="password" className="block text-sm font-medium">Senha</label>
          <input id="password" type="password" {...register('password', { required: true })} />
        </div>
        <button type="submit" disabled={formState.isSubmitting} className="w-full">
          {formState.isSubmitting ? 'Entrando...' : 'Entrar'}
        </button>
      </form>
    </div>
  );
}
