import { NavLink, Outlet } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';

const links = [
  { to: '/acessos/novo', label: 'Novo Acesso' },
  { to: '/acessos/saida', label: 'Leitor QR' },
  { to: '/relatorios', label: 'Relatórios' }
];

export default function Layout() {
  const { logout } = useAuth();

  return (
    <div className="min-h-screen flex">
      <aside className="w-64 bg-secondary p-6 space-y-6">
        <div>
          <h1 className="text-2xl font-bold">Controle de Acesso</h1>
          <p className="text-sm text-slate-400">Órgão Público</p>
        </div>
        <nav className="space-y-2">
          {links.map((link) => (
            <NavLink
              key={link.to}
              to={link.to}
              className={({ isActive }) =>
                `block px-4 py-2 rounded ${isActive ? 'bg-primary text-white' : 'text-slate-200 hover:bg-slate-700'}`
              }
            >
              {link.label}
            </NavLink>
          ))}
        </nav>
        <button type="button" onClick={logout} className="w-full bg-red-600 hover:bg-red-700">
          Sair
        </button>
      </aside>
      <main className="flex-1 p-8 bg-slate-950">
        <Outlet />
      </main>
    </div>
  );
}
