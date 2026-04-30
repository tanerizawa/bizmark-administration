import { test, expect } from '@playwright/test'

test('client can login and reach dashboard', async ({ page }) => {
  await page.goto('/client/login')
  await page.getByLabel('Email').fill('e2e-client@example.com')
  await page.locator('input[name="password"]').fill('password')
  await page.locator('button[type="submit"]').first().click()

  await expect(page).toHaveURL(/\/client\/dashboard/)
})
