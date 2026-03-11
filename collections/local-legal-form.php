<?php

return function ($site) {  
  $currentLanguage = kirby()->language()->code();

  $pages = $site->index()->filter(function ($page) use ($currentLanguage) {


    if ($page->kem_email_legal_form()->exists() && $page->content($currentLanguage)->kem_email_legal_form()->toBool()) {
      return true;
    }

    if (!$blueprint = $page->blueprint()) {
      return false;
    }

    foreach ($blueprint->fields() as $fieldName => $fieldOptions) {
      if (($fieldOptions['type'] ?? null) !== 'blocks') {
        continue;
      }

      $blocksField = $page->content($currentLanguage)->$fieldName();
      if ($blocksField->isEmpty()) {
        continue;
      }

      foreach ($blocksField->toBlocks() as $block) {
        if ($block->type() === 'email-manager') {
          if ($block->kem_email_legal_form()->toBool()) {
            return true;
          }
        }
      }
    }

    return false;
  });

  $mappedPages = $pages->map(function ($page) {
    return [
      'id' => $page->id(),
      'text' => $page->title()->value(),
      'link' => $page->panel()->url(),
      'image' => [
        'icon' => 'document',
        'back' => 'pattern',
        'src' => $page->image() ? $page->image()->url() : null,
        'color' => 'gray-500',
      ],
    ];
  })->values(); 

  return $mappedPages;
};