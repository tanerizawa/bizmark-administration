import { test, expect } from '@playwright/test'

test('admin can login and access permit management', async ({ page }) => {
  await page.goto('/login')
  await page.getByLabel('Email').fill('e2e-admin@example.com')
  await page.locator('input[name="password"]').fill('password')
  await page.locator('button[type="submit"]').first().click()

  await page.goto('/admin/permits')
  await expect(page).toHaveURL(/\/admin\/permits/)
})
