import { useEffect, useState } from 'react';
import { format } from 'date-fns';
import { useAuth } from '../hooks/useAuth';

export default function ReportsPage() {
  const { api } = useAuth();
  const [filters, setFilters] = useState({ from: '', to: '' });
  const [data, setData] = useState([]);

  const loadData = async () => {
    const params = {};
    if (filters.from) params.from = filters.from;
    if (filters.to) params.to = filters.to;
    const { data } = await api.get('/reports/summary', { params });
    setData(data);
  };

  useEffect(() => {
    loadData().catch((error) => console.error(error));
  }, []);

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-semibold">Relatórios</h2>
        <p className="text-slate-400">Acompanhe acessos por dia e setor.</p>
      </div>
      <div className="bg-slate-900 p-6 rounded space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label className="block text-sm font-medium">Data Inicial</label>
            <input type="date" value={filters.from} onChange={(e) => setFilters({ ...filters, from: e.target.value })} />
          </div>
          <div>
            <label className="block text-sm font-medium">Data Final</label>
            <input type="date" value={filters.to} onChange={(e) => setFilters({ ...filters, to: e.target.value })} />
          </div>
          <div className="flex items-end">
            <button type="button" onClick={loadData} className="w-full">
              Filtrar
            </button>
          </div>
        </div>
        <table className="min-w-full divide-y divide-slate-800">
          <thead>
            <tr className="text-left text-slate-400">
              <th className="py-2">Dia</th>
              <th className="py-2">Setor</th>
              <th className="py-2 text-right">Total</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-800">
            {data.map((row) => (
              <tr key={`${row.day}-${row.department_name}`}>
                <td className="py-2">{format(new Date(row.day), 'dd/MM/yyyy')}</td>
                <td className="py-2">{row.department_name}</td>
                <td className="py-2 text-right">{row.total}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
