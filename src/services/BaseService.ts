import { ServiceConfig, ServiceResponse } from '../types';

/**
 * Base Service Class
 * All service classes extend this base class for common functionality
 */
export abstract class BaseService {
  protected config: ServiceConfig;

  constructor(config: ServiceConfig = {}) {
    this.config = {
      timeout: 5000,
      retryAttempts: 3,
      ...config,
    };
  }

  /**
   * Wraps service calls with error handling
   */
  protected async executeService<T>(
    operation: () => Promise<T>
  ): Promise<ServiceResponse<T>> {
    try {
      const data = await operation();
      return {
        success: true,
        data,
        message: 'Operation completed successfully',
      };
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Unknown error occurred',
      };
    }
  }

  /**
   * Simulates API call delay (for demo purposes)
   */
  protected async simulateDelay(ms: number = 100): Promise<void> {
    return new Promise((resolve) => setTimeout(resolve, ms));
  }

  /**
   * Validates required fields
   */
  protected validateRequired(data: any, fields: string[]): void {
    const missing = fields.filter((field) => !data[field]);
    if (missing.length > 0) {
      throw new Error(`Missing required fields: ${missing.join(', ')}`);
    }
  }
}
