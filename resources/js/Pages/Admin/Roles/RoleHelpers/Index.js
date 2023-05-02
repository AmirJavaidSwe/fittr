export const RoleHelpers = () => {

    function collectAllPermissions (data) {
        let allPermissionIds = []
        for (let i = 0; i < data.length; i++) {
            const m_permissions = data[i].permissions
            for (let j = 0; j < m_permissions.length; j++) {
                if(!allPermissionIds.includes(m_permissions[j].id)) {
                    allPermissionIds.push(m_permissions[j].id)
                }
            }
        }
        return allPermissionIds
    }

    function togglePermission (permissions, permissionId, allPermissionIds) {
        let allowAllPermissions = false
        if (permissions.includes(permissionId)) {
            permissions = permissions.filter(id => id !== permissionId)
        } else {
            permissions.push(permissionId)
        }
        permissions.sort((a, b) => new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'}).compare(a, b));
    
        if(permissions.length == allPermissionIds.length) {
            allowAllPermissions = true
        }

        return {
            permissions: permissions,
            allowAllPermissions: allowAllPermissions
        }
    }

    function toggleAllPermission(allowAllPermissions, modules) {
        let permission_ids = []
        if (allowAllPermissions == true) {
            allowAllPermissions = false
            return {
                allowAllPermissions: allowAllPermissions
            }
        }
        let permissions = [];
        for (let i = 0; i < modules.length; i++) {
            const m_permissions = modules[i].permissions
            for (let j = 0; j < m_permissions.length; j++) {
                if(!permissions.includes(m_permissions[j].id)) {
                    permissions.push(m_permissions[j].id)
                }
            }
        }
        permissions.sort((a, b) => new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'}).compare(a, b));
        allowAllPermissions = true
        return {
            allowAllPermissions: allowAllPermissions,
            permissions: permissions
        }
    }

    function roleHasPermission(rolePermissions, permissions, role) {
        for (let i = 0; i < role.permissions.length; i++) {
            if (!rolePermissions.includes(role.permissions[i].id)) {
                rolePermissions.push(role.permissions[i].id)
                if (!permissions.includes(role.permissions[i].id)) {
                    permissions.push(role.permissions[i].id)
                }
            }
        }
        return {
            rolePermissions: rolePermissions,
            permissions: permissions
        }
    }

    return {
        collectAllPermissions,
        togglePermission,
        toggleAllPermission,
        roleHasPermission
    };
};
