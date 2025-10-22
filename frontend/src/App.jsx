import { Navigate, Route, Routes } from 'react-router-dom';
import { AuthProvider, useAuth } from './hooks/useAuth';
import LoginPage from './pages/Login.jsx';
import NewAccessPage from './pages/NewAccess.jsx';
import QrScannerPage from './pages/QrScanner.jsx';
import ReportsPage from './pages/Reports.jsx';
import Layout from './components/Layout.jsx';

function ProtectedRoute({ children }) {
  const { token } = useAuth();
  if (!token) {
    return <Navigate to="/login" replace />;
  }
  return children;
}

export default function App() {
  return (
    <AuthProvider>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route
          path="/"
          element={(
            <ProtectedRoute>
              <Layout />
            </ProtectedRoute>
          )}
        >
          <Route index element={<Navigate to="/acessos/novo" replace />} />
          <Route path="/acessos/novo" element={<NewAccessPage />} />
          <Route path="/acessos/saida" element={<QrScannerPage />} />
          <Route path="/relatorios" element={<ReportsPage />} />
        </Route>
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </AuthProvider>
  );
}
