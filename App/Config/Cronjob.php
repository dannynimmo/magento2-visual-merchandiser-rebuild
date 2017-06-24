<?php

namespace DannyNimmo\VisualMerchandiserRebuild\App\Config;

use Magento\Framework\Exception\LocalizedException;

class Cronjob extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     * @throws \Zend_Validate_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value     = $this->getValue();
        $validator = \Zend_Validate::is(
            $value,
            'Regex',
            ['pattern' => '/^[0-9,\-\?\/\*\ ]+$/']
        );

        if (!$validator) {
            $message = __(
                'Please correct Cron Expression: "%1".',
                $value
            );
            throw new LocalizedException($message);
        }

        return $this;
    }
}