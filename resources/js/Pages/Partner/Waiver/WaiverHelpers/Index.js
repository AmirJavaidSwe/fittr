import find from "lodash/find";

export const WaiverHelpers = () => {

    function getShowPlaces () {
        const obj = [{
            label: "On Signup",
            value: "sign-up"
        },
        {
            label: "Upon Adding a Family Member",
            value: "family-add"
        },
        {
            label: "On Checkout",
            value: "checkout"
        }]
        return obj
    }
    function questionTypes () {
        return ["Yes/No", "Checkbox", "Free Text"]
    }

    function getShowAtValue (val) {
        return find(getShowPlaces(), (o) => {
            return (o.value === val) ? o.label : '';
        });
    }

    return {
        getShowPlaces,
        questionTypes,
        getShowAtValue
    };
};
