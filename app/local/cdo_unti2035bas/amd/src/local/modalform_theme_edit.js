/**
 * vim: ts=2 sw=2 sts=2
 * @module     local_cdo_unti2035bas/local/modalform_theme_edit
 */

import ModalForm from 'core_form/modalform';
import {get_string} from 'core/str';

class ModalFormThemeEdit extends ModalForm {
  constructor(id = 0) {
    super({
      formClass: 'local_cdo_unti2035bas\\form\\theme_edit',
      args: { id, },
    });
  }

  async show() {
    try {
      return await super.show();
    } finally {
      if (this.config.args.id) {
        this.modal.setTitle(get_string('themeedit', 'local_cdo_unti2035bas'));
      } else {
        this.modal.setTitle(get_string('themecreate', 'local_cdo_unti2035bas'));
      }
    }
  }
}

export default ModalFormThemeEdit;

