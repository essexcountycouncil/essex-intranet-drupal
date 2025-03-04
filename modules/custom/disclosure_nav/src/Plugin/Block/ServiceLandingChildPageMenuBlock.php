<?php

declare(strict_types=1);

namespace Drupal\disclosure_nav\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a ServiceLandingChildPageMenu nav block.
 *
 * @Block(
 *   id = "disclosure_nav_service_landing_child_page_menu",
 *   admin_label = @Translation("Service Landing Child Pages Menu"),
 *   category = @Translation("Custom"),
 * )
 */
final class ServiceLandingChildPageMenuBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected CurrentRouteMatch $currentRouteMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'referenced_landing_page' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $node = $storage->load($this->configuration['referenced_landing_page'][0]['target_id'] ?? 0);

    $form['referenced_landing_page'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Service landing page'),
      '#target_type' => 'node',
      '#default_value' => $node ?? NULL,
      '#selection_settings' => ['target_bundles' => ['localgov_services_landing']],
      '#tags' => TRUE,
      '#size' => 30,
      '#maxlength' => 1024,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['referenced_landing_page'] = $form_state->getValue('referenced_landing_page');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $menu = [];
    $menu['#items'] = [];
    if ($this->configuration['referenced_landing_page']) {
      $storage = $this->entityTypeManager->getStorage('node');
      $node = $storage->load($this->configuration['referenced_landing_page'][0]['target_id']);
      $menu['#heading_label'] = $node->getTitle();
      if ($node instanceof NodeInterface) {
        /** @var EntityReferenceFieldItemList $child_pages */
        $child_pages = $node->get('localgov_destinations');
        if ($child_pages) {
          try {
            $child_page_entities = $child_pages->referencedEntities();
            if ($child_page_entities) {
              foreach ($child_page_entities as $service) {
                if ($service instanceof NodeInterface) {
                  $menu['#items'][] = [
                    'label' => $service->title->value,
                    'url' => $service->toUrl(),
                    'is_active' => $service->toUrl()->getRouteParameters()['node'] === $this->currentRouteMatch->getCurrentRouteMatch()->getRawParameters()->get('node'),
                  ];
                }
              }
            }
          }
          catch (\Exception $e) {
            \Drupal::logger('disclosure_nav')->notice('Could not retrieve any child page references from the selected service landing page %title.', [
              '%title' => $node->getTitle(),
            ]);
          }
        }
      }
    }
    $menu['#theme'] = 'service_landing_page_child_pages_nav';
    $build['output'] = $menu;

    return $build;
  }

}
