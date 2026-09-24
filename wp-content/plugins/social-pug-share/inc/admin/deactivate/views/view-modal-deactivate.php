<div id="dpsp-deactivate-modal" style="display: none;">
        
    <div id="dpsp-deactivate-wrapper">

        <form action="" method="post">

            <!-- Deactivation form header -->
            <div id="dpsp-deactivate-header">
                <img src="<?php echo DPSP_PLUGIN_DIR_URL; ?>inc/admin/feedback-form/assets/img/corgi-100x100.png" />
                <h3><strong><?php echo __( 'We would love to hear your feedback...', 'social-pug' ); ?></strong></h3>
            </div>

            <!-- Deactivation form inner -->
            <div id="dpsp-deactivate-inner">

                <!-- Textarea details field -->
                <p><strong><?php echo __( "Your feedback helps us build a better plugin. Please let us know what makes you deactivate Social Pug.", 'social-pug' ) ?></strong></p>
        	    <textarea name="dpsp_disable_text" placeholder="<?php echo __( 'Your feedback here...', 'social-pug' ); ?>"></textarea>

                <hr />

                <!-- Contact back checkbox field -->
                <p><strong><?php echo __( "We believe in the power of open communication, that's why we would like to reach back to you.", 'social-pug' ) ?></strong></p>
                <label>
                    <input type="checkbox" value="yes" name="dpsp_contact_me" />
                    <?php echo __( 'Check this if you wish to be contacted by our team to have a quick chat about your feedback.', 'social-pug' ); ?>
                </label>

                <!-- Email field -->
                <div id="dpsp-deactivate-contact-me">

                    <hr />

                    <label><strong><?php echo __( "Let us know your email address so that we can write back to you:", 'social-pug' ); ?></strong></label>
            	    <input type="email" name="dpsp_disable_from" value="" placeholder="<?php echo __( 'Your email address', 'social-pug' ); ?>" />
                </div>

            </div>

            <!-- Deactivation form footer -->
            <div id="dpsp-deactivate-footer">
                <input disabled id="dpsp-feedback-submit" class="button button-primary" type="submit" name="dpsp-feedback-submit" value="<?php echo __( 'Submit & Deactivate', 'social-pug' ); ?>"/>
                <a id="dpsp-only-deactivate" class="button"><?php echo __( 'Only Deactivate', 'social-pug'); ?></a>
                <a class="dpsp-deactivate-close" href="#"><?php echo __( 'Don\'t deactivate', 'social-pug'); ?></a>
            </div>

        </form>

    </div>

</div>