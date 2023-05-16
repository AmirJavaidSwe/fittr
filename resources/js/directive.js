export default {
    install: (app) => {
        app.directive('can', {
            mounted(el, binding, vnode) {
                const { module, roles, permission, user } = binding.value;
                if(user.is_super) return true;
                let hasPermission = false;

                for (const key in roles) {
                    const roleKey = key; // The key (e.g., 'financial-controller')
                    const roleValue = roles[key]; // The corresponding value (e.g., 'Financial Controller')
                    if (
                        user &&
                        user.role_permissions &&
                        user.role_permissions[module] &&
                        user.role_permissions[module][roleKey] &&
                        user.role_permissions[module][roleKey].includes(permission)
                    ) {
                        hasPermission = true;
                        break;
                    }
                }
                
                if (!hasPermission) {
                    el.parentNode?.removeChild(el);
                }
            },
            unmounted(el) {
                el.parentNode?.removeChild(el);
            },
        });
    },
};
