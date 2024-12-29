CREATE TABLE IF NOT EXISTS `cities`
(
    `id`   int PRIMARY KEY AUTO_INCREMENT,
    `name` varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `treatments`
(
    `id`         int PRIMARY KEY AUTO_INCREMENT,
    `name`       varchar(255)   NOT NULL,
    `price`      decimal(10, 2) NOT NULL,
    `duration`   int            NOT NULL,
    `created_at` datetime       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime                DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `medical_centers`
(
    `id`         int PRIMARY KEY AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `phone`      varchar(20),
    `fax`        varchar(20),
    `whatsapp`   varchar(20),
    `email`      varchar(255),
    `city_id`    int,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime              DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`)
);

CREATE TABLE IF NOT EXISTS `working_hours`
(
    `id`                int PRIMARY KEY AUTO_INCREMENT,
    `medical_center_id` int,
    `day_of_week`       int      NOT NULL,
    `opening_time`      time     NOT NULL,
    `closing_time`      time     NOT NULL,
    `created_at`        datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        datetime          DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`)
);

CREATE TABLE IF NOT EXISTS `prescription_templates`
(
    `id`           int PRIMARY KEY AUTO_INCREMENT,
    `name`         varchar(255) NOT NULL,
    `content`      text         NOT NULL,
    `treatment_id` int,
    `created_at`   datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   datetime              DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`)
);

CREATE TABLE IF NOT EXISTS `patient_funds`
(
    `id`                int PRIMARY KEY AUTO_INCREMENT,
    `name`              varchar(255)                 NOT NULL,
    `contribution_type` enum ('percentage', 'fixed') NOT NULL,
    `created_at`        datetime                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        datetime                              DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `roles`
(
    `id`         int PRIMARY KEY AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime              DEFAULT CURRENT_TIMESTAMP
);

-- Create PERMISSION table
CREATE TABLE IF NOT EXISTS `permissions`
(
    `id`   int PRIMARY KEY AUTO_INCREMENT,
    `name` varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `role_permissions`
(
    `role_id`       int,
    `permission_id` int,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
);

CREATE TABLE IF NOT EXISTS `users`
(
    `id`                int PRIMARY KEY AUTO_INCREMENT,
    `name`              varchar(255)        NOT NULL,
    `phone`             varchar(20),
    `email`             varchar(255) UNIQUE,
    `username`          varchar(255) UNIQUE NOT NULL,
    `password`          varchar(255)        NOT NULL,
    `role_id`           int,
    `medical_center_id` int,
    `created_at`        datetime            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        datetime                     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
    FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`)
);

CREATE TABLE IF NOT EXISTS `doctor_schedules`
(
    `id`                int PRIMARY KEY AUTO_INCREMENT,
    `user_id`           int,
    `medical_center_id` int,
    `day_of_week`       int      NOT NULL,
    `start_time`        time     NOT NULL,
    `end_time`          time     NOT NULL,
    `created_at`        datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        datetime          DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`)
);

CREATE TABLE IF NOT EXISTS `patients`
(
    `id`             int PRIMARY KEY AUTO_INCREMENT,
    `full_name`      varchar(255)            NOT NULL,
    `gender`         enum ('male', 'female') NOT NULL,
    `id_number`      varchar(20) UNIQUE      NOT NULL,
    `birth_date`     date                    NOT NULL,
    `guardian_phone` varchar(20),
    `patient_phone`  varchar(20),
    `city_id`        int,
    `created_at`     datetime                NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     datetime                         DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`)
);

CREATE TABLE IF NOT EXISTS `patient_fund_associations`
(
    `id`              int PRIMARY KEY AUTO_INCREMENT,
    `patient_id`      int,
    `patient_fund_id` int,
    `contribution_percentage`
                      decimal(5, 2) NOT NULL,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
    FOREIGN KEY (`patient_fund_id`) REFERENCES `patient_funds` (`id`)
);

CREATE TABLE IF NOT EXISTS `folders`
(
    `id`               int PRIMARY KEY AUTO_INCREMENT,
    `name`             varchar(255) NOT NULL,
    `parent_folder_id` int,
    `patient_id`       int,
    `created_at`       datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       datetime              DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_folder_id`) REFERENCES `folders` (`id`),
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`)
);

CREATE TABLE IF NOT EXISTS `files`
(
    `id`         int PRIMARY KEY AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `file_path`  varchar(255) NOT NULL,
    `folder_id`  int,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime              DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`)
);

# CREATE TABLE IF NOT EXISTS `appointments`
# (
#     `id`                int PRIMARY KEY AUTO_INCREMENT,
#     `patient_id`        int,
#     `doctor_id`         int,
#     `medical_center_id` int,
#     `treatment_id`      int,
#     `appointment_time`  datetime      NOT NULL,
#     `status`            enum ('scheduled', 'waiting', 'completed',
#         'cancelled')                  NOT NULL,
#     `price`
#                         decimal(5, 2) NOT NULL,
#     `discount`
#                         decimal(5, 2) NOT NULL,
#     patient_fund_id     int,
#     patient_fund_amount decimal(5, 2) NOT NULL,
#     `duration`          int           NOT NULL,
#     FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
#     FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
#     FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`),
#     FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`),
#     FOREIGN KEY (`patient_fund_id`) REFERENCES `patient_funds` (`id`)
# );
CREATE TABLE IF NOT EXISTS `appointments`
(
    `id`                int PRIMARY KEY AUTO_INCREMENT,
    `patient_id`        int,
    `doctor_id`         int,
    `medical_center_id` int,
    `treatment_id`      int,
    `appointment_time`  datetime                                                                                          NOT NULL,
    `status`            enum ('reserved', 'waiting', 'completed', 'not
attended without telling', 'not_attended_with_telling') NOT NULL,
    `duration`          int                                                                                               NOT NULL,
    `created_at`        datetime                                                                                          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        datetime                                                                                                   DEFAULT CURRENT_TIMESTAMP,

    `created_by`        int                                                                                               NOT NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
    FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`),
    FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`)
);
CREATE TABLE IF NOT EXISTS `invoices`
(
    `id`                  int PRIMARY KEY AUTO_INCREMENT,
    `appointment_id`      int,
    `price`               decimal(10, 2) NOT NULL,
    `discount`            decimal(10, 2) NOT NULL,
    `patient_id`          int,
    `patient_fund_id`     int,
    `patient_fund_amount` decimal(10, 2) NOT NULL,
    `total`               decimal(10, 2) NOT NULL,
    `created_at`          datetime       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          datetime                DEFAULT CURRENT_TIMESTAMP,
    `created_by`          int            NOT NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`),
    FOREIGN KEY (`patient_fund_id`) REFERENCES `patient_funds` (`id`)
);


CREATE TABLE IF NOT EXISTS `system_logs`
(
    `id`                int PRIMARY KEY AUTO_INCREMENT,
    `user_id`           int          NOT NULL,
    `table`             varchar(20)  NOT NULL,
    `event_type`        varchar(255) NOT NULL,
    `event_description` text         NOT NULL,
    `timestamp`         datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);
CREATE TABLE IF NOT EXISTS `settings`
(
    `id`         int PRIMARY KEY AUTO_INCREMENT,
    `key`        varchar(255) NOT NULL,
    `value`      text         NOT NULL,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime              DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS `banks`
(
    `id`         int PRIMARY KEY AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `number`     varchar(255) NOT NULL,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime              DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS `checks`
(
    `id`             int PRIMARY KEY AUTO_INCREMENT,
    `bank_id`        int            NOT NULL,
    `account_number` varchar(255)   NOT NULL,
    `check_number`   varchar(255)   NOT NULL,
    `amount`         decimal(10, 2) NOT NULL,
    `date`           date           NOT NULL,
    `created_at`     datetime       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     datetime                DEFAULT CURRENT_TIMESTAMP,
    `created_by`     int            NOT NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
    FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`)
);
CREATE TABLE IF NOT EXISTS `payments`
(
    `id`           int PRIMARY KEY AUTO_INCREMENT,
    `payment_type` enum ('cash','visa', 'check', 'bank transfer') NOT NULL,
    `amount`       decimal(10, 2)                                 NOT NULL,
    `attachment`   varchar(255),
    `date`         date                                           NOT NULL,
    `created_at`   datetime                                       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   datetime                                                DEFAULT CURRENT_TIMESTAMP,
    `created_by`   int                                            NOT NULL,
    `check_id`     int,
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
    FOREIGN KEY (`check_id`) REFERENCES `checks` (`id`)
);
CREATE TABLE IF NOT EXISTS `payment_invoices`
(
    `payment_id` int,
    `invoice_id` int,
    PRIMARY KEY (`payment_id`, `invoice_id`),
    FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`),
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`)
);
