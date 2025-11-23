/**
 * Culqi QR Core SDK
 * @packageDocumentation
 */

import EventEmitter from 'eventemitter3';
import { APIClient } from './api/client';
import { QRGenerator } from './qr/generator';
import type {
  CulqiConfig,
  QRCreateParams,
  QRData,
  Payment,
  EventType,
  EventHandler,
} from './types';

export * from './types';

/**
 * Main Culqi QR SDK class
 */
export class CulqiQR extends EventEmitter {
  private apiClient: APIClient;
  private qrGenerator: QRGenerator;

  /**
   * QR management
   */
  public qr: {
    create: (params: QRCreateParams) => Promise<QRData>;
    get: (id: string) => Promise<QRData>;
    isValid: (qr: QRData) => boolean;
  };

  constructor(config: CulqiConfig) {
    super();

    // Validate config
    if (!config.publicKey) {
      throw new Error('Public key is required');
    }

    // Initialize API client
    this.apiClient = new APIClient(config);

    // Initialize QR generator
    this.qrGenerator = new QRGenerator(this.apiClient);

    // Setup QR methods
    this.qr = {
      create: this.createQR.bind(this),
      get: this.getQR.bind(this),
      isValid: this.qrGenerator.isValid.bind(this.qrGenerator),
    };
  }

  /**
   * Create a QR code for payment
   */
  private async createQR(params: QRCreateParams): Promise<QRData> {
    try {
      const qr = await this.qrGenerator.create(params);

      // Emit event
      this.emit('qr.generated', qr);

      return qr;
    } catch (error) {
      this.emit('error', error);
      throw error;
    }
  }

  /**
   * Get QR data by ID
   */
  private async getQR(id: string): Promise<QRData> {
    try {
      return await this.qrGenerator.get(id);
    } catch (error) {
      this.emit('error', error);
      throw error;
    }
  }

  /**
   * Get payment status
   */
  public async getPaymentStatus(paymentId: string): Promise<Payment> {
    try {
      const response = await this.apiClient.get<any>(`/charges/${paymentId}`);

      return {
        id: response.id,
        orderId: response.order?.id,
        amount: response.amount / 100,
        currency: response.currency_code,
        status: this.mapPaymentStatus(response.outcome?.type),
        description: response.description,
        email: response.email,
        metadata: response.metadata,
        createdAt: new Date(response.creation_date * 1000),
        updatedAt: new Date(response.update_date * 1000),
      };
    } catch (error) {
      this.emit('error', error);
      throw error;
    }
  }

  /**
   * Verify webhook signature
   */
  public verifyWebhook(payload: string, signature: string, secret: string): boolean {
    // Implement HMAC signature verification
    const crypto = require('crypto');
    const expectedSignature = crypto
      .createHmac('sha256', secret)
      .update(payload)
      .digest('hex');

    return crypto.timingSafeEqual(
      Buffer.from(signature),
      Buffer.from(expectedSignature)
    );
  }

  /**
   * Listen to events
   */
  public on(event: EventType, handler: EventHandler): this {
    return super.on(event, handler);
  }

  /**
   * Remove event listener
   */
  public off(event: EventType, handler: EventHandler): this {
    return super.off(event, handler);
  }

  /**
   * Listen to event once
   */
  public once(event: EventType, handler: EventHandler): this {
    return super.once(event, handler);
  }

  private mapPaymentStatus(outcome: string): Payment['status'] {
    const statusMap: Record<string, Payment['status']> = {
      authorized: 'completed',
      pending: 'pending',
      declined: 'failed',
      expired: 'expired',
      cancelled: 'cancelled',
    };

    return statusMap[outcome] || 'pending';
  }

  /**
   * Get SDK version
   */
  public static get version(): string {
    return '0.1.0';
  }
}

/**
 * Create a new instance of CulqiQR
 */
export function createCulqiQR(config: CulqiConfig): CulqiQR {
  return new CulqiQR(config);
}

// Default export
export default CulqiQR;
