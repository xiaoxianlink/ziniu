ALTER TABLE `cw_order`
ADD COLUMN `so_type`  int(2) NULL DEFAULT 1 AFTER `so_id`;