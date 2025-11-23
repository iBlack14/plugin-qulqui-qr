/**
 * QR Generator
 */

import type { QRCreateParams, QRData, Currency } from '../types';
import { APIClient } from '../api/client';

export class QRGenerator {
  private client: APIClient;

  constructor(client: APIClient) {
    this.client = client;
  }

  /**
   * Create a QR code for payment
   */
  public async create(params: QRCreateParams): Promise<QRData> {
    // Validate params
    this.validateParams(params);

    // Convert amount to cents
    const amountInCents = Math.round(params.amount * 100);

    // Prepare order data
    const orderData = {
      amount: amountInCents,
      currency_code: params.currency,
      description: params.description || 'Payment',
      order_number: params.orderId,
      client_details: params.email
        ? {
            email: params.email,
          }
        : undefined,
      metadata: params.metadata,
      expiration_date: params.expirationTime
        ? this.calculateExpiration(params.expirationTime)
        : undefined,
    };

    try {
      // Create order via API
      const response = await this.client.post<any>('/orders', orderData);

      // Generate QR code
      const qrCode = this.generateQRCode(response.id);

      return {
        id: response.id,
        qrCode,
        amount: params.amount,
        currency: params.currency,
        status: 'pending',
        description: params.description,
        createdAt: new Date(response.creation_date * 1000),
        expiresAt: response.expiration_date
          ? new Date(response.expiration_date * 1000)
          : undefined,
        metadata: params.metadata,
      };
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get QR data by ID
   */
  public async get(id: string): Promise<QRData> {
    try {
      const response = await this.client.get<any>(`/orders/${id}`);

      return {
        id: response.id,
        qrCode: this.generateQRCode(response.id),
        amount: response.amount / 100,
        currency: response.currency_code as Currency,
        status: this.mapStatus(response.state),
        description: response.description,
        createdAt: new Date(response.creation_date * 1000),
        expiresAt: response.expiration_date
          ? new Date(response.expiration_date * 1000)
          : undefined,
        metadata: response.metadata,
      };
    } catch (error) {
      throw error;
    }
  }

  /**
   * Check if QR is still valid
   */
  public isValid(qr: QRData): boolean {
    if (qr.status !== 'pending') {
      return false;
    }

    if (qr.expiresAt && qr.expiresAt < new Date()) {
      return false;
    }

    return true;
  }

  private validateParams(params: QRCreateParams): void {
    if (!params.amount || params.amount <= 0) {
      throw new Error('Amount must be greater than 0');
    }

    if (!params.currency) {
      throw new Error('Currency is required');
    }

    if (!['PEN', 'USD'].includes(params.currency)) {
      throw new Error('Currency must be PEN or USD');
    }
  }

  private generateQRCode(paymentId: string): string {
    // Generate QR code URL using a QR service
    // In production, this might use Culqi's QR endpoint or a dedicated QR library
    const qrData = encodeURIComponent(paymentId);
    return `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${qrData}`;
  }

  private calculateExpiration(seconds: number): number {
    return Math.floor(Date.now() / 1000) + seconds;
  }

  private mapStatus(state: string): QRData['status'] {
    const statusMap: Record<string, QRData['status']> = {
      pending: 'pending',
      processing: 'processing',
      paid: 'completed',
      expired: 'expired',
      cancelled: 'cancelled',
    };

    return statusMap[state] || 'pending';
  }
}
