function parseFlag ( targetArgument, argumentType ) {
	const processArguments = process.argv;

	if ( processArguments.includes( targetArgument ) ) {

		if ( argumentType === 'string' ) {
			return processArguments[processArguments.indexOf( targetArgument ) + 1];
		}
	
		if ( argumentType === 'array' ) {
	
			let flagsIndex = [];

			for ( const key in processArguments ) {
				if ( processArguments[key].charAt( 0 ) === '-' && processArguments[key].charAt( 0 ) === '-' ) {
					flagsIndex.push( [ processArguments[key], key ] );
				}
			}

			for ( const key in flagsIndex ) {
				if ( flagsIndex[key][0] === targetArgument ) {
					if ( flagsIndex[key][1] < flagsIndex[flagsIndex.length - 1][1] ) {
						return processArguments.slice( Number( flagsIndex[key][1] ) + 1, flagsIndex[Number( key ) + 1][1] );
					} else {
						return processArguments.slice( Number( flagsIndex[key][1] ) + 1 );
					}
				}
			}
		}
	
		if ( argumentType === 'boolean' ) {
			return true;
		}
	
		if ( argumentType === 'number' ) {
			return Number( processArguments[processArguments.indexOf( targetArgument ) + 1] );
		}
	}

	return false;
}

module.exports = parseFlag;
