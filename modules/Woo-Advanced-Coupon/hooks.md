# ToDo

---

- customly validate coupons
- take action before & after default coupons function
- add custom discount type [ "Admin" ]
- add custom filters [ "Admin" ]
- add custom discounts [ "Admin" ]
- add custom rules [ "Admin" ]

# Admin Hooks

---

## sdwac_coupon_discount_type

> Ajax.php:72

These Filters Hooks can be used for filter discount type fields options ;

---

## sdwac_coupon_filters

> Ajax.php:116

These Filters Hooks can be used to add custom filters ;

---

# Front Hooks

---

## sdwac_coupon_brefore_wp_loaded

> sdwac_coupon_auto.php:31

These Action Hooks will be run before sdwac_coupon_first_order & sdwac_coupon_auto_coupon ;

---

## sdwac_coupon_after_wp_loaded

> sdwac_coupon_auto.php:34

These Action Hooks will be run after sdwac_coupon_first_order & sdwac_coupon_auto_coupon ;

---

## sdwac_coupon_validator

> Validator.php:23

These Filter Hooks can be use for validate coupons. return True & False ;

---
