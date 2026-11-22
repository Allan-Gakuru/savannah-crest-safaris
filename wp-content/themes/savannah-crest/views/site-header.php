<?php
namespace SavannahCrest\Theme;

defined( 'ABSPATH' ) || exit;
?>
<div class="scs-mega" data-scs-navigation>
	<div class="scs-mega__utility">
		<div class="scs-mega__shell scs-mega__utility-links">
			<a href="<?php echo esc_url( CONTACT_PHONE_URL ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5c-1 0-2 1-2 2 0 8.8 7.2 16 16 16 1 0 2-1 2-2v-3l-5-2-2 2a11 11 0 0 1-6-6l2-2-2-5Z"/></svg>
				<?php echo esc_html( CONTACT_PHONE ); ?>
			</a>
			<a href="<?php echo esc_url( 'mailto:' . CONTACT_EMAIL ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
				<?php esc_html_e( 'Email us', 'savannah-crest' ); ?>
			</a>
			<?php if ( $planner ) : ?><a class="scs-mega__planning" href="<?php echo esc_url( $planner ); ?>" data-scs-enquiry="header"><?php esc_html_e( 'Plan a tailor-made safari', 'savannah-crest' ); ?> <span aria-hidden="true">&rarr;</span></a><?php endif; ?>
		</div>
	</div>
	<div class="scs-mega__bar">
		<div class="scs-mega__shell scs-mega__bar-inner">
			<?php site_mark(); ?>
			<?php if ( $panels || $stories ) : ?>
				<button class="scs-mega__toggle" type="button" hidden data-scs-mobile aria-controls="<?php echo esc_attr( $nav_id ); ?>" aria-expanded="false">
					<span aria-hidden="true"></span><?php esc_html_e( 'Menu', 'savannah-crest' ); ?>
				</button>
				<nav id="<?php echo esc_attr( $nav_id ); ?>" class="scs-mega__nav" data-scs-links aria-label="<?php esc_attr_e( 'Main navigation', 'savannah-crest' ); ?>">
					<ul class="scs-mega__items" role="list">
						<?php foreach ( $panels as $panel ) : ?>
							<li>
								<?php if ( ! $panel['groups'] && empty( $panel['feature']['url'] ) ) : ?>
									<a class="scs-mega__trigger" href="<?php echo esc_url( $panel['url'] ); ?>"><?php echo esc_html( $panel['label'] ); ?></a>
								<?php else : ?>
									<details data-scs-panel>
										<summary class="scs-mega__trigger"><?php echo esc_html( $panel['label'] ); ?><span class="scs-mega__chevron" aria-hidden="true"></span></summary>
										<div class="scs-mega__panel">
											<div class="scs-mega__shell scs-mega__panel-inner">
												<div class="scs-mega__directory">
													<div class="scs-mega__groups">
														<?php foreach ( $panel['groups'] as $group ) : ?>
															<div class="scs-mega__group">
																<p><?php echo esc_html( $group['title'] ); ?></p>
																<ul role="list">
																	<?php foreach ( $group['links'] as $label => $url ) : ?>
																		<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
																	<?php endforeach; ?>
																</ul>
															</div>
														<?php endforeach; ?>
													</div>
													<a class="scs-mega__all" href="<?php echo esc_url( $panel['url'] ); ?>"><?php echo esc_html( $panel['all_label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
												</div>
												<?php if ( ! empty( $panel['feature']['url'] ) ) : ?>
													<a class="scs-mega__feature" href="<?php echo esc_url( $panel['feature']['url'] ); ?>">
														<?php
														$image = empty( $panel['feature']['attachment'] ) ? '' : wp_get_attachment_image(
															$panel['feature']['attachment'], 'medium_large', false, array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' )
														);
														if ( $image ) {
															echo $image; // WordPress attachment markup.
														} else {
															?>
															<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/' . $panel['feature']['image'] ) ); ?>" width="840" height="560" alt="" loading="lazy" decoding="async">
															<?php
														}
														?>
														<span><?php echo esc_html( $panel['feature']['label'] ); ?><span aria-hidden="true">&rarr;</span></span>
													</a>
												<?php endif; ?>
											</div>
										</div>
									</details>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
						<?php if ( $stories ) : ?><li class="scs-mega__mobile-stories"><a class="scs-mega__trigger" href="<?php echo esc_url( $stories ); ?>"><?php esc_html_e( 'Traveller stories', 'savannah-crest' ); ?></a></li><?php endif; ?>
					</ul>
				</nav>
			<?php endif; ?>
			<div class="scs-mega__actions">
				<?php if ( $stories ) : ?><a class="scs-mega__stories" href="<?php echo esc_url( $stories ); ?>"><?php esc_html_e( 'Traveller stories', 'savannah-crest' ); ?></a><?php endif; ?>
				<details class="scs-mega__search" data-scs-search>
					<summary aria-label="<?php esc_attr_e( 'Search', 'savannah-crest' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><circle cx="10.75" cy="10.75" r="6.25"/><path d="m15.4 15.4 4.4 4.4"/></svg>
					</summary>
					<form class="scs-mega__search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label for="<?php echo esc_attr( $search_id ); ?>"><?php esc_html_e( 'Search Savannah Crest Safaris', 'savannah-crest' ); ?></label>
						<div>
							<input id="<?php echo esc_attr( $search_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query( false ) ); ?>" required>
							<button class="scs-button" type="submit"><?php esc_html_e( 'Search', 'savannah-crest' ); ?></button>
						</div>
					</form>
				</details>
				<?php if ( $enquiry || $planner ) : ?><a class="scs-button scs-mega__enquire" data-scs-enquiry="header" href="<?php echo esc_url( $enquiry ?: $planner ); ?>"><?php esc_html_e( 'Enquire', 'savannah-crest' ); ?></a><?php endif; ?>
			</div>
		</div>
	</div>
</div>
