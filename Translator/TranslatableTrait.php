<?php

namespace Sidus\EAVModelBundle\Translator;

use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

trait TranslatableTrait
{
    /**
     * @var TranslatorInterface
     * @\JMS\Serializer\Annotation\Exclude()
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Will check the translator for the key "eav.family.{$code}.label"
     * and humanize the code if no translation is found
     *
     * @param string|array $tIds
     * @param array        $parameters
     * @param string       $fallback
     * @param bool         $humanizeFallback
     * @return string
     */
    protected function tryTranslate($tIds, array $parameters = [], $fallback = null, $humanizeFallback = true)
    {
        if (!is_array($tIds)) {
            $tIds = [$tIds];
        }
        foreach ($tIds as $tId) {
            try {
                if ($this->translator instanceof TranslatorBagInterface) {
                    if ($this->translator->getCatalogue()->has($tId)) {
                        /** @noinspection PhpUndefinedMethodInspection */

                        return $this->translator->trans($tId, $parameters);
                    }
                } elseif ($this->translator instanceof TranslatorInterface) {
                    $label = $this->translator->trans($tId, $parameters);
                    if ($label !== $tId) {
                        return $label;
                    }
                }
            } catch (\InvalidArgumentException $e) {
                // Do nothing
            }
        }

        if ($fallback === null) {
            return null;
        }
        if (!$humanizeFallback) {
            return $fallback;
        }
        $pattern = '/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]|\d{1,}/';

        return str_replace('_', ' ', preg_replace($pattern, ' $0', $fallback));
    }
}
