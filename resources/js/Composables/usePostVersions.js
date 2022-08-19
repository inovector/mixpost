const usePostVersions = () => {
    const versionContentObject = (body = '', media = []) => {
        return {
            body,
            media
        };
    }

    const versionObject = (accountId = 0, isDefault = false) => {
        return {
            account_id: accountId,
            is_default: isDefault,
            content: [versionContentObject()]
        }
    }

    const getDefaultVersion = (versions) => {
        const find = versions.find((version) => {
            return version.is_default && version.account_id === 0;
        })

        return find ? find : null;
    }

    const getAccountVersion = (versions, accountId) => {
        const find = versions.find((version) => {
            return version.account_id === accountId;
        })

        return find ? find : null;
    }

    return {
        versionContentObject,
        versionObject,
        getDefaultVersion,
        getAccountVersion
    }
}

export default usePostVersions;
