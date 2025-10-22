import { createContext, useContext, useEffect, useMemo, useState } from 'react';
import Cookies from 'js-cookie';
import { useLocation, useNavigate } from 'react-router-dom';
import { useApi } from './useApi';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [token, setToken] = useState(() => Cookies.get('token'));
  const [user, setUser] = useState(null);
  const navigate = useNavigate();
  const location = useLocation();
  const api = useApi(token);

  useEffect(() => {
    if (!token) {
      return;
    }

    api
      .get('/me')
      .then(({ data }) => setUser(data))
      .catch(() => {
        Cookies.remove('token');
        setToken(undefined);
        setUser(null);
        if (location.pathname !== '/login') {
          navigate('/login');
        }
      });
  }, [token, api, location.pathname, navigate]);

  const login = async (credentials) => {
    const { data } = await api.post('/login', credentials);
    Cookies.set('token', data.token, { sameSite: 'Strict' });
    setToken(data.token);
    setUser(data.user);
    navigate('/acessos/novo');
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } catch (error) {
      console.error('Erro ao sair', error);
    }
    Cookies.remove('token');
    setToken(undefined);
    setUser(null);
    navigate('/login');
  };

  const value = useMemo(() => ({ token, user, setUser, login, logout, api }), [token, user, api]);

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) {
    throw new Error('useAuth precisa estar dentro de AuthProvider');
  }
  return ctx;
}
