import axios from 'axios';
import { useMemo } from 'react';

export function useApi(token) {
  return useMemo(() => {
    const instance = axios.create({
      baseURL: '/api'
    });

    instance.interceptors.request.use((config) => {
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    });

    return instance;
  }, [token]);
}
