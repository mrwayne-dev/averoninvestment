-- ============================================================
-- Averon Investment Platform — Full Database Schema
-- MySQL 8.4.3 · InnoDB · utf8mb4_unicode_ci
-- Created by Wayne
-- ============================================================

CREATE DATABASE IF NOT EXISTS `averon_investment`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `averon_investment`;

SET FOREIGN_KEY_CHECKS = 0;


-- ============================================================
-- 1. USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`             BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `first_name`     VARCHAR(100)     NOT NULL,
  `last_name`      VARCHAR(100)     NOT NULL,
  `email`          VARCHAR(255)     NOT NULL,
  `password`       VARCHAR(255)     NOT NULL,
  `role`           ENUM('user','admin') NOT NULL DEFAULT 'user',
  `status`         ENUM('pending','active','suspended','banned') NOT NULL DEFAULT 'pending',
  `region`         VARCHAR(100)     NULL,
  `language`       VARCHAR(50)      NULL,
  `avatar`         VARCHAR(255)     NULL,
  `referral_code`  VARCHAR(20)      NULL,
  `referred_by`    BIGINT UNSIGNED  NULL,
  `last_login`     TIMESTAMP        NULL,
  `created_at`     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email`          (`email`),
  UNIQUE KEY `uq_users_referral_code`  (`referral_code`),
  KEY `idx_users_email`                (`email`),
  KEY `idx_users_referral_code`        (`referral_code`),
  CONSTRAINT `fk_users_referred_by`
    FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 2. WALLETS
-- ============================================================
CREATE TABLE IF NOT EXISTS `wallets` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         BIGINT UNSIGNED NOT NULL,
  `balance`         DECIMAL(18,2)   NOT NULL DEFAULT 0.00,
  `profit_balance`  DECIMAL(18,2)   NOT NULL DEFAULT 0.00,
  `invested_amount` DECIMAL(18,2)   NOT NULL DEFAULT 0.00,
  `created_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wallets_user_id` (`user_id`),
  KEY `idx_wallets_user_id`       (`user_id`),
  CONSTRAINT `fk_wallets_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 3. INVESTMENT PLANS
-- ============================================================
CREATE TABLE IF NOT EXISTS `investment_plans` (
  `id`                           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                         VARCHAR(100)    NOT NULL,
  `description`                  TEXT            NULL,
  `min_amount`                   DECIMAL(18,2)   NOT NULL,
  `max_amount`                   DECIMAL(18,2)   NULL,
  `duration_days`                INT             NOT NULL,
  `daily_yield_min`              DECIMAL(5,2)    NOT NULL,
  `daily_yield_max`              DECIMAL(5,2)    NOT NULL,
  `total_yield_min`              DECIMAL(6,2)    NOT NULL,
  `total_yield_max`              DECIMAL(6,2)    NOT NULL,
  `compounding_type`             ENUM('simple','compound') NOT NULL DEFAULT 'simple',
  `capital_locked`               BOOLEAN         NOT NULL DEFAULT 1,
  `profit_withdrawal_after_days` INT             NOT NULL,
  `dedicated_manager`            BOOLEAN         NOT NULL DEFAULT 0,
  `color_accent`                 VARCHAR(7)      NULL,
  `badge_label`                  VARCHAR(30)     NULL,
  `is_active`                    BOOLEAN         NOT NULL DEFAULT 1,
  `sort_order`                   INT             NOT NULL DEFAULT 0,
  `created_by`                   BIGINT UNSIGNED NULL,
  `created_at`                   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_investment_plans_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 4. USER INVESTMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_investments` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`               BIGINT UNSIGNED NOT NULL,
  `plan_id`               BIGINT UNSIGNED NOT NULL,
  `amount`                DECIMAL(18,2)   NOT NULL,
  `daily_yield_rate`      DECIMAL(5,2)    NOT NULL COMMENT 'Snapshot of avg rate at time of investment',
  `profit_earned`         DECIMAL(18,2)   NOT NULL DEFAULT 0.00,
  `status`                ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `profit_available_date` DATE            NOT NULL COMMENT 'start_date + profit_withdrawal_after_days',
  `start_date`            DATE            NOT NULL,
  `end_date`              DATE            NOT NULL COMMENT 'start_date + duration_days',
  `last_profit_credited`  DATE            NULL,
  `created_at`            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_investments_user_status` (`user_id`, `status`),
  CONSTRAINT `fk_user_investments_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_investments_plan_id`
    FOREIGN KEY (`plan_id`) REFERENCES `investment_plans` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 5. MEMBERSHIP PLANS
-- ============================================================
CREATE TABLE IF NOT EXISTS `membership_plans` (
  `id`                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                    VARCHAR(100)    NOT NULL,
  `description`             TEXT            NULL,
  `price`                   DECIMAL(10,2)   NOT NULL,
  `duration_days`           INT             NOT NULL DEFAULT 30,
  `max_active_investments`  INT             NULL COMMENT 'NULL = unlimited',
  `withdrawal_speed_hours`  INT             NOT NULL DEFAULT 72,
  `referral_commission_pct` DECIMAL(4,2)    NOT NULL DEFAULT 0.00,
  `priority_support`        ENUM('standard','priority','dedicated','manager') NOT NULL DEFAULT 'standard',
  `has_analytics`           BOOLEAN         NOT NULL DEFAULT 0,
  `has_strategy_reports`    BOOLEAN         NOT NULL DEFAULT 0,
  `access_elite_plans`      BOOLEAN         NOT NULL DEFAULT 0,
  `invitation_pools`        BOOLEAN         NOT NULL DEFAULT 0,
  `color_accent`            VARCHAR(7)      NULL,
  `badge_icon`              VARCHAR(30)     NULL,
  `benefits`                JSON            NULL,
  `is_active`               BOOLEAN         NOT NULL DEFAULT 1,
  `sort_order`              INT             NOT NULL DEFAULT 0,
  `created_at`              TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`              TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 6. USER MEMBERSHIPS
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_memberships` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         BIGINT UNSIGNED NOT NULL,
  `plan_id`         BIGINT UNSIGNED NOT NULL,
  `commission_rate` DECIMAL(4,2)    NOT NULL COMMENT 'Snapshot of rate at enrollment',
  `status`          ENUM('active','expired') NOT NULL DEFAULT 'active',
  `start_date`      DATE            NOT NULL,
  `end_date`        DATE            NOT NULL,
  `created_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_user_memberships_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_memberships_plan_id`
    FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 7. TRANSACTIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `transactions` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED NOT NULL,
  `type`         ENUM('deposit','withdrawal','profit','membership_fee','referral_bonus','fee','transfer_sent','transfer_received') NOT NULL,
  `amount`       DECIMAL(18,2)   NOT NULL,
  `currency`     VARCHAR(20)     NOT NULL DEFAULT 'USD',
  `status`       ENUM('pending','confirmed','failed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `reference`    VARCHAR(100)    NULL,
  `notes`        TEXT            NULL,
  `processed_at` TIMESTAMP       NULL,
  `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_transactions_user_type_status` (`user_id`, `type`, `status`),
  CONSTRAINT `fk_transactions_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 8. NOWPAYMENTS ORDERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `nowpayments_orders` (
  `id`                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`                BIGINT UNSIGNED NOT NULL,
  `transaction_id`         BIGINT UNSIGNED NOT NULL,
  `nowpayments_payment_id` VARCHAR(64)     NOT NULL,
  `pay_currency`           VARCHAR(10)     NOT NULL,
  `pay_amount`             DECIMAL(18,8)   NOT NULL,
  `pay_address`            VARCHAR(255)    NOT NULL,
  `price_amount`           DECIMAL(18,2)   NOT NULL,
  `price_currency`         VARCHAR(10)     NOT NULL DEFAULT 'USD',
  `payment_status`         VARCHAR(30)     NOT NULL,
  `qr_code_url`            VARCHAR(500)    NULL,
  `created_at`             TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`             TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nowpayments_payment_id` (`nowpayments_payment_id`),
  CONSTRAINT `fk_nowpayments_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nowpayments_transaction_id`
    FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 9. REFERRALS
-- ============================================================
CREATE TABLE IF NOT EXISTS `referrals` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `referrer_id`     BIGINT UNSIGNED NOT NULL,
  `referred_id`     BIGINT UNSIGNED NOT NULL,
  `commission_rate` DECIMAL(4,2)    NOT NULL,
  `total_earned`    DECIMAL(18,2)   NOT NULL DEFAULT 0.00,
  `created_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_referrals_referred_id` (`referred_id`),
  CONSTRAINT `fk_referrals_referrer_id`
    FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_referrals_referred_id`
    FOREIGN KEY (`referred_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 10. EMAIL VERIFICATIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `code`       VARCHAR(6)      NOT NULL,
  `expires_at` TIMESTAMP       NOT NULL,
  `used_at`    TIMESTAMP       NULL,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_email_verifications_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 11. PASSWORD RESETS
-- ============================================================
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `token`      VARCHAR(64)     NOT NULL,
  `expires_at` TIMESTAMP       NOT NULL,
  `used_at`    TIMESTAMP       NULL,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_password_resets_token` (`token`),
  CONSTRAINT `fk_password_resets_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 12. NOTIFICATIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `title`      VARCHAR(255)    NOT NULL,
  `message`    TEXT            NOT NULL,
  `type`       VARCHAR(50)     NOT NULL,
  `is_read`    BOOLEAN         NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user_is_read` (`user_id`, `is_read`),
  CONSTRAINT `fk_notifications_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 13. TESLA STOCKS
-- ============================================================
CREATE TABLE IF NOT EXISTS `tesla_stocks` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `symbol`         VARCHAR(10)     NOT NULL,
  `price`          DECIMAL(10,2)   NOT NULL,
  `change_amount`  DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `change_percent` DECIMAL(6,2)    NOT NULL DEFAULT 0.00,
  `volume`         BIGINT          NOT NULL DEFAULT 0,
  `market_cap`     BIGINT          NULL,
  `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 14. SITE SETTINGS
-- ============================================================
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key`   VARCHAR(100)    NOT NULL,
  `setting_value` TEXT            NULL,
  `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_site_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================
-- SEED DATA
-- ============================================================

-- ------------------------------------------------------------
-- Admin User
-- Password: Admin@2024
-- Hash: password_hash('Admin@2024', PASSWORD_BCRYPT, ['cost' => 12])
-- ------------------------------------------------------------
INSERT INTO `users`
  (`first_name`, `last_name`, `email`, `password`, `role`, `status`, `referral_code`)
VALUES
  ('Admin', 'User', 'admin@averon-investment.com',
   '$2y$12$ti0Q4y5mmRoInb6C7JUTlu5aMdiLeW1qe2.PHENs5j.KWWnsoM1HC',
   'admin', 'active', 'ADMIN001');

-- Wallet for admin
INSERT INTO `wallets` (`user_id`, `balance`, `profit_balance`, `invested_amount`)
VALUES (1, 0.00, 0.00, 0.00);


-- ------------------------------------------------------------
-- Investment Plans
-- ------------------------------------------------------------
INSERT INTO `investment_plans`
  (`name`, `description`, `min_amount`, `max_amount`, `duration_days`,
   `daily_yield_min`, `daily_yield_max`, `total_yield_min`, `total_yield_max`,
   `compounding_type`, `capital_locked`, `profit_withdrawal_after_days`,
   `dedicated_manager`, `color_accent`, `badge_label`, `is_active`, `sort_order`, `created_by`)
VALUES
  ('Launch Plan',
   'Perfect entry-level plan for new investors. Earn steady daily returns with minimal risk.',
   100.00, 999.00, 30,
   0.20, 0.30, 6.00, 9.00,
   'simple', 1, 30,
   0, '#2196F3', 'Starter', 1, 1, 1),

  ('Drive Plan',
   'Accelerate your portfolio with higher yields. Popular among intermediate investors.',
   1000.00, 9999.00, 45,
   0.35, 0.50, 15.00, 22.00,
   'simple', 1, 25,
   0, '#00C851', 'Popular', 1, 2, 1),

  ('Performance Plan',
   'High-performance returns for serious investors. Advanced yield structure with priority access.',
   10000.00, 49999.00, 60,
   0.60, 0.80, 36.00, 48.00,
   'simple', 1, 40,
   0, '#FFB300', 'Advanced', 1, 3, 1),

  ('Plaid Elite Plan',
   'The ultimate investment tier. Compound growth, dedicated manager, and exclusive benefits.',
   50000.00, NULL, 90,
   0.90, 1.20, 81.00, 108.00,
   'compound', 1, 70,
   1, '#BA2D0B', 'Exclusive', 1, 4, 1);


-- ------------------------------------------------------------
-- Membership Plans
-- ------------------------------------------------------------
INSERT INTO `membership_plans`
  (`name`, `description`, `price`, `duration_days`,
   `max_active_investments`, `withdrawal_speed_hours`, `referral_commission_pct`,
   `priority_support`, `has_analytics`, `has_strategy_reports`,
   `access_elite_plans`, `invitation_pools`,
   `color_accent`, `badge_icon`, `benefits`, `is_active`, `sort_order`)
VALUES
  ('Basic Member',
   'Get started on the Averon Investment platform with essential features.',
   49.00, 30,
   2, 72, 3.00,
   'standard', 0, 0, 0, 0,
   '#A0A0A0', 'user',
   '["Up to 2 active investments","3% referral commission","Standard support","72h withdrawal processing","Access to Launch & Drive plans"]',
   1, 1),

  ('Silver Member',
   'More investment slots and faster withdrawals for growing portfolios.',
   99.00, 30,
   5, 24, 5.00,
   'priority', 0, 0, 0, 0,
   '#C0C0C0', 'medal',
   '["Up to 5 active investments","5% referral commission","Priority support","24h withdrawal processing","Access to Launch & Drive plans"]',
   1, 2),

  ('Gold Member',
   'Unlock elite plans and advanced analytics to maximise your returns.',
   199.00, 30,
   10, 12, 7.00,
   'dedicated', 1, 0, 1, 0,
   '#FFB300', 'crown',
   '["Up to 10 active investments","7% referral commission","Dedicated support","12h withdrawal processing","Access to all plans including Performance","Portfolio analytics dashboard"]',
   1, 3),

  ('Platinum Member',
   'The pinnacle of membership. Unlimited investments, fastest withdrawals, and quarterly strategy reports.',
   499.00, 30,
   NULL, 1, 10.00,
   'manager', 1, 1, 1, 1,
   '#E5E4E2', 'diamond',
   '["Unlimited active investments","10% referral commission","Personal account manager","1h withdrawal processing","Access to all plans including Plaid Elite","Portfolio analytics dashboard","Quarterly strategy reports","Exclusive invitation pools"]',
   1, 4);


-- ------------------------------------------------------------
-- Site Settings
-- ------------------------------------------------------------
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
  ('min_deposit',                      '50'),
  ('max_deposit',                      '500000'),
  ('min_withdrawal',                   '50'),
  ('withdrawal_fee_pct',               '1.5'),
  ('nowpayments_supported_currencies', 'btc,eth,usdttrc20,usdterc20'),
  ('platform_name',                    'Averon Investment'),
  ('platform_email',                   'support@averon-investment.com'),
  ('maintenance_mode',                 '0'),
  ('referral_enabled',                 '1');


-- ------------------------------------------------------------
-- TSLA Stock Placeholder
-- ------------------------------------------------------------
INSERT INTO `tesla_stocks` (`symbol`, `price`, `change_amount`, `change_percent`, `volume`)
VALUES ('TSLA', 250.00, 0.00, 0.00, 0);
