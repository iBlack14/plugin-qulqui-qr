/**
 * Core types for Culqi QR SDK
 */

export type Environment = 'sandbox' | 'production';

export type Currency = 'PEN' | 'USD';

export type PaymentStatus =
  | 'pending'
  | 'processing'
  | 'completed'
  | 'failed'
  | 'expired'
  | 'cancelled';

export interface CulqiConfig {
  /**
   * Public API key
   */
  publicKey: string;

  /**
   * Secret API key (backend only)
   */
  secretKey?: string;

  /**
   * Environment
   */
  environment?: Environment;

  /**
   * API base URL override
   */
  apiBaseUrl?: string;

  /**
   * Enable debug logging
   */
  debug?: boolean;

  /**
   * Request timeout in ms
   */
  timeout?: number;

  /**
   * Enable caching
   */
  cache?: boolean;

  /**
   * Cache TTL in seconds
   */
  cacheTTL?: number;
}

export interface QRCreateParams {
  /**
   * Payment amount (in base currency units, e.g., 100.00)
   */
  amount: number;

  /**
   * Currency code
   */
  currency: Currency;

  /**
   * Payment description
   */
  description?: string;

  /**
   * Customer email
   */
  email?: string;

  /**
   * Order ID (your internal reference)
   */
  orderId?: string;

  /**
   * Additional metadata
   */
  metadata?: Record<string, any>;

  /**
   * Expiration time in seconds
   */
  expirationTime?: number;
}

export interface QRData {
  /**
   * Payment ID
   */
  id: string;

  /**
   * QR code image URL or data
   */
  qrCode: string;

  /**
   * Payment amount
   */
  amount: number;

  /**
   * Currency
   */
  currency: Currency;

  /**
   * Payment status
   */
  status: PaymentStatus;

  /**
   * Description
   */
  description?: string;

  /**
   * Creation timestamp
   */
  createdAt: Date;

  /**
   * Expiration timestamp
   */
  expiresAt?: Date;

  /**
   * Metadata
   */
  metadata?: Record<string, any>;
}

export interface Payment {
  /**
   * Payment ID
   */
  id: string;

  /**
   * Order ID
   */
  orderId?: string;

  /**
   * Amount
   */
  amount: number;

  /**
   * Currency
   */
  currency: Currency;

  /**
   * Status
   */
  status: PaymentStatus;

  /**
   * Description
   */
  description?: string;

  /**
   * Customer email
   */
  email?: string;

  /**
   * Metadata
   */
  metadata?: Record<string, any>;

  /**
   * Created at
   */
  createdAt: Date;

  /**
   * Updated at
   */
  updatedAt: Date;

  /**
   * Completed at
   */
  completedAt?: Date;
}

export interface WebhookEvent {
  /**
   * Event type
   */
  event: string;

  /**
   * Event data
   */
  data: Payment;

  /**
   * Timestamp
   */
  timestamp: Date;
}

export interface APIError {
  /**
   * Error code
   */
  code: string;

  /**
   * Error message
   */
  message: string;

  /**
   * HTTP status code
   */
  status?: number;

  /**
   * Additional error details
   */
  details?: any;
}

export type EventType =
  | 'payment.created'
  | 'payment.processing'
  | 'payment.completed'
  | 'payment.failed'
  | 'payment.expired'
  | 'payment.cancelled'
  | 'qr.generated'
  | 'qr.scanned'
  | 'error';

export type EventHandler = (data: any) => void;

export interface CacheEntry<T> {
  data: T;
  expiresAt: number;
}
