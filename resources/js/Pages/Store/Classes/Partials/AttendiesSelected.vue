<script setup>
import { watch } from 'vue';
import find from 'lodash/find';
const props = defineProps(["user", "current_class", 'selected_family_members', 'is_family_booking', 'other_family_member_booking_ids']);
const getFamilyMemberName = (id) => {
    const member = find(props.user.family, (o) => o.id == id)
    return member.name
}

const getFamilyMemberNameFromUserBooking = (user_booking) => {
    if(!user_booking.family_member_id && props.user.id == user_booking.user_id) {
        return props.user.name
    } else {
        const fmID = user_booking.family_member_id
        const member = find(props.user.family, (o) => o.id == fmID)
        return member.name
    }
}
</script>
<template>
    <div
        class="mt-5 bg-mainbg border-rounded text-right border-gray p-4"
        v-if="props.user?.family?.length && !props.current_class.is_booked"
    >
        <template
            v-if="
                props.is_family_booking == 2 &&
                props.selected_family_members.family_member.ids.length
            "
        >
            {{
                parseInt(props.selected_family_members.family_member.ids.length) +
                1 +
                " x Attendee(s) Selected"
            }}
            <br />
            <template v-if="props.selected_family_members.user_id">
                <small>
                    {{ user.name }}
                </small>
            </template>
            <template
                v-if="parseInt(props.selected_family_members.family_member.ids.length)"
            >
                <template
                    v-for="(
                        familyMemberId, familyMemberIndex
                    ) in props.selected_family_members.family_member.ids"
                    :key="familyMemberIndex"
                >
                <br />
                <small>{{ getFamilyMemberName(familyMemberId) }}</small>
                </template>
            </template>
        </template>
        <template v-else>
            {{ 1 + " x Attendee(s) Selected" }}
            <br />
            <small>
                {{ user.name }}
            </small>
        </template>
    </div>
    <div
        class="mt-5 bg-mainbg border-rounded text-right border-gray p-4"
        v-if="
            props.user?.family?.length &&
            props.current_class.is_booked &&
            props.current_class.user_bookings.length != props.user.family.length + 1
        "
    >
        {{
            parseInt(props.current_class.user_bookings.length) +
            " out of " +
            (props.user.family.length + 1) +
            " Attendee(s) Selected"
        }}
        <template
                v-if="parseInt(props.current_class.user_bookings.length)"
            >
                <template
                    v-for="(
                        user_booking, ubi
                    ) in props.current_class.user_bookings"
                    :key="ubi"
                >
                <br />
                <small>{{ getFamilyMemberNameFromUserBooking(user_booking) }}</small>
                </template>
            </template>
    </div>
</template>
