import { BaseService } from '../BaseService';
import { MealPlan, DiningHall, MenuItem, ServiceResponse } from '../../types';

/**
 * Dining Service
 * Handles meal plans, dining hall information, and dietary accommodations
 */
export class DiningService extends BaseService {
  /**
   * Get available meal plans
   */
  async getMealPlans(): Promise<ServiceResponse<MealPlan[]>> {
    return this.executeService(async () => {
      await this.simulateDelay();

      const plans: MealPlan[] = [
        {
          id: 'PLAN-UNLIMITED',
          name: 'Unlimited Plan',
          mealsPerWeek: -1, // unlimited
          diningDollars: 200,
          price: 2500,
        },
        {
          id: 'PLAN-14',
          name: '14 Meals Per Week',
          mealsPerWeek: 14,
          diningDollars: 300,
          price: 2200,
        },
        {
          id: 'PLAN-10',
          name: '10 Meals Per Week',
          mealsPerWeek: 10,
          diningDollars: 400,
          price: 1900,
        },
        {
          id: 'PLAN-COMMUTER',
          name: 'Commuter Plan',
          mealsPerWeek: 5,
          diningDollars: 500,
          price: 1200,
        },
      ];

      return plans;
    });
  }

  /**
   * Get student's current meal plan
   */
  async getStudentMealPlan(studentId: string): Promise<ServiceResponse<{
    plan: MealPlan;
    mealsRemaining: number;
    diningDollarsBalance: number;
    weekStart: Date;
    weekEnd: Date;
  }>> {
    return this.executeService(async () => {
      this.validateRequired({ studentId }, ['studentId']);
      await this.simulateDelay();

      const today = new Date();
      const weekStart = new Date(today);
      weekStart.setDate(today.getDate() - today.getDay());
      const weekEnd = new Date(weekStart);
      weekEnd.setDate(weekStart.getDate() + 6);

      return {
        plan: {
          id: 'PLAN-14',
          name: '14 Meals Per Week',
          mealsPerWeek: 14,
          diningDollars: 300,
          price: 2200,
        },
        mealsRemaining: 8,
        diningDollarsBalance: 145.50,
        weekStart,
        weekEnd,
      };
    });
  }

  /**
   * Change meal plan
   */
  async changeMealPlan(studentId: string, planId: string): Promise<ServiceResponse<{ effectiveDate: Date }>> {
    return this.executeService(async () => {
      this.validateRequired({ studentId, planId }, ['studentId', 'planId']);
      await this.simulateDelay();

      const effectiveDate = new Date();
      effectiveDate.setDate(effectiveDate.getDate() + 7); // Next week

      return { effectiveDate };
    });
  }

  /**
   * Get dining hall information
   */
  async getDiningHalls(): Promise<ServiceResponse<DiningHall[]>> {
    return this.executeService(async () => {
      await this.simulateDelay();

      const halls: DiningHall[] = [
        {
          id: 'HALL-MAIN',
          name: 'Main Dining Hall',
          location: 'Student Center',
          hours: [
            { day: 'Monday-Friday', openTime: '07:00', closeTime: '21:00' },
            { day: 'Saturday-Sunday', openTime: '08:00', closeTime: '20:00' },
          ],
        },
        {
          id: 'HALL-CAFE',
          name: 'Campus Cafe',
          location: 'Library Building',
          hours: [
            { day: 'Monday-Friday', openTime: '07:30', closeTime: '18:00' },
            { day: 'Saturday', openTime: '09:00', closeTime: '15:00' },
          ],
        },
      ];

      return halls;
    });
  }

  /**
   * Get today's menu for a dining hall
   */
  async getMenu(diningHallId: string, date?: Date): Promise<ServiceResponse<{
    breakfast: MenuItem[];
    lunch: MenuItem[];
    dinner: MenuItem[];
  }>> {
    return this.executeService(async () => {
      this.validateRequired({ diningHallId }, ['diningHallId']);
      await this.simulateDelay();

      return {
        breakfast: [
          {
            name: 'Scrambled Eggs',
            category: 'Main',
            calories: 180,
            allergens: ['Eggs', 'Dairy'],
            dietary: ['Vegetarian'],
          },
          {
            name: 'Oatmeal',
            category: 'Main',
            calories: 150,
            allergens: [],
            dietary: ['Vegan', 'Vegetarian'],
          },
        ],
        lunch: [
          {
            name: 'Grilled Chicken',
            category: 'Main',
            calories: 320,
            allergens: [],
            dietary: ['Gluten-Free'],
          },
          {
            name: 'Veggie Burger',
            category: 'Main',
            calories: 280,
            allergens: ['Soy'],
            dietary: ['Vegan', 'Vegetarian'],
          },
        ],
        dinner: [
          {
            name: 'Pasta Primavera',
            category: 'Main',
            calories: 420,
            allergens: ['Wheat', 'Dairy'],
            dietary: ['Vegetarian'],
          },
          {
            name: 'Baked Salmon',
            category: 'Main',
            calories: 380,
            allergens: ['Fish'],
            dietary: ['Gluten-Free'],
          },
        ],
      };
    });
  }

  /**
   * Register dietary restrictions
   */
  async registerDietaryRestrictions(
    studentId: string,
    restrictions: {
      allergies: string[];
      dietary: string[];
      notes?: string;
    }
  ): Promise<ServiceResponse<{ registered: boolean }>> {
    return this.executeService(async () => {
      this.validateRequired({ studentId, restrictions }, ['studentId', 'restrictions']);
      await this.simulateDelay();

      return { registered: true };
    });
  }

  /**
   * Get dining history
   */
  async getDiningHistory(studentId: string, startDate: Date, endDate: Date): Promise<ServiceResponse<Array<{
    date: Date;
    hall: string;
    mealType: string;
    cost?: number;
  }>>> {
    return this.executeService(async () => {
      this.validateRequired({ studentId, startDate, endDate }, ['studentId', 'startDate', 'endDate']);
      await this.simulateDelay();

      return [];
    });
  }

  /**
   * Add dining dollars to account
   */
  async addDiningDollars(studentId: string, amount: number): Promise<ServiceResponse<{
    newBalance: number;
    transactionId: string;
  }>> {
    return this.executeService(async () => {
      this.validateRequired({ studentId, amount }, ['studentId', 'amount']);

      if (amount <= 0) {
        throw new Error('Amount must be greater than zero');
      }

      await this.simulateDelay();

      return {
        newBalance: 245.50,
        transactionId: `TRANS-${Date.now()}`,
      };
    });
  }
}
