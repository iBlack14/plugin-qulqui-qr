/**
 * API Client
 */

import axios, { AxiosInstance, AxiosError } from 'axios';
import type { CulqiConfig, APIError } from '../types';

const API_BASE_URL = {
  production: 'https://api.culqi.com/v2',
  sandbox: 'https://api-dev.culqi.com/v2',
};

export class APIClient {
  private client: AxiosInstance;
  private config: Required<CulqiConfig>;

  constructor(config: CulqiConfig) {
    this.config = {
      publicKey: config.publicKey,
      secretKey: config.secretKey || '',
      environment: config.environment || 'sandbox',
      apiBaseUrl:
        config.apiBaseUrl || API_BASE_URL[config.environment || 'sandbox'],
      debug: config.debug || false,
      timeout: config.timeout || 30000,
      cache: config.cache !== false,
      cacheTTL: config.cacheTTL || 3600,
    };

    this.client = axios.create({
      baseURL: this.config.apiBaseUrl,
      timeout: this.config.timeout,
      headers: {
        'Content-Type': 'application/json',
      },
    });

    this.setupInterceptors();
  }

  private setupInterceptors(): void {
    // Request interceptor
    this.client.interceptors.request.use(
      (config) => {
        // Add authorization header
        if (this.config.secretKey) {
          config.headers.Authorization = `Bearer ${this.config.secretKey}`;
        }

        // Debug logging
        if (this.config.debug) {
          console.log('[Culqi QR] Request:', {
            method: config.method,
            url: config.url,
            data: config.data,
          });
        }

        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Response interceptor
    this.client.interceptors.response.use(
      (response) => {
        if (this.config.debug) {
          console.log('[Culqi QR] Response:', response.data);
        }
        return response;
      },
      (error: AxiosError) => {
        const apiError = this.handleError(error);

        if (this.config.debug) {
          console.error('[Culqi QR] Error:', apiError);
        }

        return Promise.reject(apiError);
      }
    );
  }

  private handleError(error: AxiosError): APIError {
    if (error.response) {
      // Server responded with error
      const data = error.response.data as any;
      return {
        code: data.code || 'api_error',
        message: data.user_message || data.message || 'An error occurred',
        status: error.response.status,
        details: data,
      };
    } else if (error.request) {
      // Request made but no response
      return {
        code: 'network_error',
        message: 'Network error - no response received',
        details: error.message,
      };
    } else {
      // Error in request setup
      return {
        code: 'request_error',
        message: error.message || 'Error setting up request',
      };
    }
  }

  public async get<T>(endpoint: string, params?: any): Promise<T> {
    const response = await this.client.get<T>(endpoint, { params });
    return response.data;
  }

  public async post<T>(endpoint: string, data?: any): Promise<T> {
    const response = await this.client.post<T>(endpoint, data);
    return response.data;
  }

  public async put<T>(endpoint: string, data?: any): Promise<T> {
    const response = await this.client.put<T>(endpoint, data);
    return response.data;
  }

  public async patch<T>(endpoint: string, data?: any): Promise<T> {
    const response = await this.client.patch<T>(endpoint, data);
    return response.data;
  }

  public async delete<T>(endpoint: string): Promise<T> {
    const response = await this.client.delete<T>(endpoint);
    return response.data;
  }

  public getConfig(): Required<CulqiConfig> {
    return { ...this.config };
  }
}
