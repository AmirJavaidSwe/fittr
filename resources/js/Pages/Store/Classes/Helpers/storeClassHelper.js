import { cloneDeep } from 'lodash';

export const storeClassHelper = () => {
    function addRemove(isParent, id, waitlistIds) {
        let ids = cloneDeep(waitlistIds);
        if (isParent) {
            const filtered = ids.filter((item) => {
                return item.is_parent && item.id == id;
            });
            if (filtered.length) {
                ids = ids.filter((item) => {
                    return item.is_parent && item.id != id;
                });
            } else {
                ids.push({
                    id: id,
                    is_parent: true,
                });
            }
        } else {
            const filtered = ids.filter((item) => {
                return !item.is_parent && item.id == id;
            });
            if (filtered.length) {
                ids = ids.filter((item) => {
                    return !item.is_parent && item.id != id;
                });
            } else {
                ids.push({
                    id: id,
                    is_parent: false,
                });
            }
        }
        return ids;
    }

    function alreadyBooked(isParent, id, classDetail, user) {
        if (isParent) {
            const filtered = classDetail.user_bookings.filter((item) => {
                return item.user_id == id && item.family_member_id == null;
            });
            return filtered.length ? true : false;
        } else if (!isParent) {
            const filtered = classDetail.user_bookings.filter((item) => {
                return (
                    item.user_id == user.id && item.family_member_id == id
                );
            });
            return filtered.length ? true : false;
        }
        return true;
    }

    function alreadyInWaitlist(isParent, id, classDetail, user) {
        const waitlist = classDetail?.waitlists;
        if (waitlist.length) {
            if (isParent) {
                const filtered = waitlist.filter((item) => {
                    return item.user_id == id && item.family_member_id == null;
                });
                return filtered.length ? true : false;
            } else if (!isParent) {
                const filtered = waitlist.filter((item) => {
                    return (
                        item.user_id == user.id &&
                        item.family_member_id == id
                    );
                });
                return filtered.length ? true : false;
            }
        }
        return false;
    }

    return {
        addRemove,
        alreadyBooked,
        alreadyInWaitlist,
    };
};
