<?php

namespace bhr\Modules\Newsletter;

class StateInput implements State
{
    protected $newsletter;
    protected $type;
    protected $inputName;
    protected $cssClass;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
        $this->inputName  = 'sm_newsletter';
        $this->type       = 'newsletter';
        $this->cssClass   = 'sm_newsletter_in';
    }

    public function setConfig($params)
    {
        $data['type'] = $this->type;

        if (!empty($params[Newsletter::NEWS_CONT])
            && is_array($params[Newsletter::NEWS_CONT])
        ) {
            $newsCont = [];
            foreach ($params[Newsletter::NEWS_CONT] as $langKey => $param) {
                $newsCont[$langKey] = htmlspecialchars($param);
            }

            $data[Newsletter::NEWS_CONT] = $newsCont;
        } else {
            $data[Newsletter::NEWS_CONT] = htmlspecialchars($params[Newsletter::NEWS_CONT]);
        }

        return $this->newsletter
            ->getModel()
            ->setNewsletterConfig($data);
    }

    public function setFront()
    {
        $config = $this->newsletter->getConfig();
        $lang = $this->newsletter->getLang();

        if (is_array($config[Newsletter::NEWS_CONT])
            && array_key_exists($this->newsletter->getLang(), $config[Newsletter::NEWS_CONT])
            && !empty($config[Newsletter::NEWS_CONT][$lang])
        ) {
            return $this->getViewItem($config[Newsletter::NEWS_CONT][$lang]);
        } elseif (isset($config[Newsletter::NEWS_CONT]['default'])
            && !empty($config[Newsletter::NEWS_CONT]['default'])
        ) {
            return $this->getViewItem($config[Newsletter::NEWS_CONT]['default']);
        }
    }

    public function getOptStateArr()
    {
        $state = $this->getSubscriberStateFromRequest();

        if ($state) {
            return [
                'forceOptIn'       => $state,
                'forceOptOut'      => !$state,
                'forcePhoneOptIn'  => $state,
                'forcePhoneOptOut' => !$state
            ];
        }

        return $this->newsletter
            ->getModel()
            ->getDefaultContactState();
    }

    public function getSubscriberStateFromRequest()
    {
        if (isset($_REQUEST[$this->inputName])) {
            return boolval($_REQUEST[$this->inputName ]);
        }
    }

    public function getViewItem($content)
    {
        return "<p class='woocommerce-FormRow form-row {$this->cssClass}'>
            <label class='woocommerce-form__label woocommerce-form__label-for-checkbox inline {$this->cssClass}' style='margin-left: 0'>
                <input class='woocommerce-form__input woocommerce-form__input-checkbox {$this->cssClass}' type='checkbox'
                       name='{$this->inputName}' checked>
                <span class='{$this->cssClass}'>{$content}</span>
            </label>
        </p>";
    }
}
